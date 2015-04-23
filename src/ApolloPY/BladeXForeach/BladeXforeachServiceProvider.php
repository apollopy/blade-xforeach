<?php namespace ApolloPY\BladeXForeach;

use Illuminate\Support\ServiceProvider;

class BladeXForeachServiceProvider extends ServiceProvider {

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
eof;

            $pattern = $compiler->createMatcher('foreach');
            if (preg_match_all($pattern, $view, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (!preg_match('/\s*\((.+)\s*as\s*([^\s]+)(\s*=>\s*([^\s]+)\s*)*\)/U', $match[2], $m)) {
                        break;
                    }

                    $val_name = $m[2];
                    if (isset($m[4])) $val_name = $m[4];
                    $val_name = substr($val_name, strpos($val_name, '$') + 1);

                    $view = str_replace($match[0], sprintf($php_format, $match[1], $val_name, $val_name, $m[1], $match[2], $val_name), $view);
                }
            }

            $pattern = $compiler->createMatcher('foreachindex');
            if (preg_match_all($pattern, $view, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (!preg_match('/\s*\(\$(.*)\)/U', $match[2], $m)) {
                        break;
                    }

                    $view = str_replace('@foreachindex' . $m[0], sprintf("(\$__x_iteration_%s - 1)", $m[1]), $view);
                }
            }

            $pattern = $compiler->createMatcher('isforeachfirst');
            if (preg_match_all($pattern, $view, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (!preg_match('/\s*\(\$(.*)\)/U', $match[2], $m)) {
                        break;
                    }

                    $view = str_replace('@isforeachfirst' . $m[0], sprintf("(\$__x_iteration_%s <= 1)", $m[1]), $view);
                }
            }

            $pattern = $compiler->createMatcher('isforeachlast');
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
