<?php namespace ApolloPY\BladeXForeach;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();
        $blade->extend( function($view, $compiler)
        {
            $php_format = <<<eof
%s
<?php
\$__x_iteration_%s = 0;
\$__x_total_%s = count(%s);
foreach%s:
\$__x_iteration_%s++;
?>
%s
eof;

            $pattern = $compiler->createMatcher('foreach');
            if (preg_match_all($pattern, $view, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (!preg_match('/\s*(\((.+)\s*as\s*([^\s]+)(\s*=>\s*([^\s]+)\s*)*\))(.*)/', $match[2], $m)) {
                        break;
                    }

                    $val_name = $m[3];
                    if (isset($m[5]) && $m[5]) $val_name = $m[5];
                    $val_name = substr($val_name, strpos($val_name, '$') + 1);

                    $view = str_replace($match[0], sprintf($php_format, $match[1], $val_name, $val_name, $m[2], $m[1], $val_name, $m[6]), $view);
                }
            }

            $pattern = '/(?<!\w)(\s*)@foreachindex(\s*\(.*\))/U';
            if (preg_match_all($pattern, $view, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (!preg_match('/\s*\(\$(.*)\)/U', $match[2], $m)) {
                        break;
                    }

                    $view = str_replace('@foreachindex' . $m[0], sprintf("(\$__x_iteration_%s - 1)", $m[1]), $view);
                }
            }

            $pattern = '/(?<!\w)(\s*)@isforeachfirst(\s*\(.*\))/U';
            if (preg_match_all($pattern, $view, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (!preg_match('/\s*\(\$(.*)\)/U', $match[2], $m)) {
                        break;
                    }

                    $view = str_replace('@isforeachfirst' . $m[0], sprintf("(\$__x_iteration_%s <= 1)", $m[1]), $view);
                }
            }

            $pattern = '/(?<!\w)(\s*)@isforeachlast(\s*\(.*\))/U';
            if (preg_match_all($pattern, $view, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (!preg_match('/\s*\(\$(.*)\)/U', $match[2], $m)) {
                        break;
                    }

                    $view = str_replace('@isforeachlast' . $m[0], sprintf("(\$__x_total_%s == \$__x_iteration_%s)", $m[1], $m[1]), $view);
                }
            }

            $pattern = $compiler->createPlainMatcher('endforeach');
            $view = preg_replace($pattern, "<?php endforeach; ?>", $view);

            return $view;
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
