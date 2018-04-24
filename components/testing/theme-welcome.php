<div class="theme-welcome">
	<div class="row">
		<div class="columns small-12 wysiwyg">
			<p><strong>Note:</strong> If you have not yet done so, make sure to run <code>npm install</code> and <code>composer install</code> in the theme root to install the necessary dependencies</p>

			<h3>Features</h3>

			<hr>

			<h4>CSS / SCSS</h4>
			<ul>
				<li>
					<h6>BEM-enabled</h6>
					<p>This starter theme is using the BEM methodology for CSS which improves modularity and helps with common specificity issues. To learn more about BEM, visit <a href="http://getbem.com/" target="_blank" rel="nofollow noopener">getbem.com</a> and <a href="https://seesparkbox.com/foundry/bem_by_example" target="_blank" rel="nofollow noopener">BEM By Example</a>.</p>
				</li>
				<li>
					<h6>Foundation 6 Flex Grid</h6>
					<p>The Foundation 6 Flex Grid is included by default. It is installed via NPM so that the current version can be easily referenced and updated if necessary.</p>
					<p>By default, only the <code>foundation-global-styles</code>, <code>foundation-flex-grid</code>, <code>foundation-visiblity-classes</code>, and <code>foundation-flex-classes</code> modules are included to keep the overall bundle size down. If you need to include additional Foundation components you can do so in <code>master.scss</code>.</p>
					<p><a href="https://foundation.zurb.com/sites/docs/flex-grid.html" target="_blank" rel="nofollow noopener">Foundation 6 Flex Grid Documentation</a></p>
				</li>
				<li>
					<h6>object-fit-images polyfill</h6>
					<p>The <a href="https://github.com/bfred-it/object-fit-images/">object-fit-images</a> polyfill has been included and will allow you to use the <code>object-fit</code> and <code>object-position</code> CSS properties in browsers that don't have support for them such as IE 11, Edge, and Safari (&lt;= 9).</p>
				</li>
			</ul>

			<hr>

			<h4>PHP</h4>
			<ul>
				<li>
					<h6>Composer</h6>
					<p>3rd party PHP packages for this theme are managed using Composer. If you do not have Composer installed you can do so by using <a href="https://brew.sh/" target="_blank" rel="nofollow noopener">Homebrew</a> <code>brew install composer</code> or by going to <a href="https://getcomposer.org/" target="_blank" rel="nofollow noopener">https://getcomposer.org/</a>.</p>
				</li>
				<li>
					<h6>Gravitate Utilities Package</h6>
					<p>The Gravitate Utilities package (repo: <a href="https://github.com/GravitateDesignStudio/grav-util">https://github.com/GravitateDesignStudio/grav-util</a> | packagist: <a href="https://packagist.org/packages/gravitate/grav-util">https://packagist.org/packages/gravitate/grav-util</a>) for Composer contains many theme-independent utility methods for WordPress and common 3rd party integrations.</p>
				</li>
			</ul>

			<hr>

			<h4>JavaScript</h4>
			<ul>
				<li>
					<h6>ESLint</h6>
					<p>ESLint is a tool that will compare the JavaScript in the theme to a defined set of rules. While it is not a part of the build process, it's highly recommended to run ESLint regularly in order to maintain consistency and prevent errors. The included <code>.eslintrc.json</code> file should be usable by linting plugins in editors such as Visual Studio Code and Atom in order to lint JS as you're editing it. To run ESLint manually, use the following command at the theme root: <code>npm run eslint</code> ESLint will hurt your feelings -- but that's a good thing.</p>
				</li>
				<li>
					<h6>Build System based on Gulp, node-sass, and Webpack</h6>
					<p>Gulp is being used as the build system for this theme and the currently configured tasks can be found in <code>gulpfile.js</code>. Node-sass is being used to parse the SCSS source files and it is configured within <code>gulpfile.js</code>. WebPack in combination with Babel is being used to transpile and minify the JavaScript source files.</p>
					<p>With the combination of Webpack and Babel, it is possible to use <a href="https://babeljs.io/learn-es2015/" target="_blank" rel="nofollow noopener">ES6+</a> features in your code without worrying about browser compatibility. Additionally, both the <a href="https://nodejs.org/docs/latest/api/modules.html" target="_blank" rel="nofollow noopener">CommonJS</a> and <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import" target="_blank" rel="nofollow noopener">ES6 Module</a> (<i>preferred</i>) formats can be used to modularize your code.</p>
				</li>
				<li>
					<h6>Included Libraries</h6>
					<strong>Colorbox</strong>
					<p>Make sure <code>require('jquery-colorbox')</code> is in your JS code and Colorbox will be added to the jQuery object where it can be used as <code>$.colorbox()</code>. <a href="#" class="colorbox-trigger" data-modal-content="This is some example modal content">Open an example modal.</a></p>

					<strong>Swiper</strong>
					<p>Swiper can be included as an ES6 module via <code>import Swiper from 'swiper'</code>. This import statement must be included in any module where you need to use Swiper. An example of how to use it is located below.</p>
				</li>
			</ul>

			<hr>
			<h3>How To Build</h3>
			<p>The following NPM script aliases have been include and can be run with <code>npm run &lt;script-name&gt;</code>:</p>
			<ul>
				<li><code>eslint</code> - Run ESLint against your JavaScript to check for any issues or errors</li>
				<li><code>build-css</code> - Run the Gulp build task for your SCSS files</li>
				<li><code>build-js</code> - Run the Gulp build task for your JavaScript files</li>
				<li><code>build</code> - Run both the CSS and JS build tasks together</li>
				<li><code>watch</code> - Start a BrowserSync proxy instance and put both build tasks into a watch state where they will trigger on any changes to the source files</li>
			</ul>
		</div>
	</div>

	<div class="block-container bg-black">
		<div class="block-inner">
			<div class="row">
				<div class="columns small-12">
					<div class="swiper-container">
						<div class="swiper-wrapper">
							<div class="swiper-slide">
								<img src="https://picsum.photos/640/480?random" alt="slide 1">
							</div>
							<div class="swiper-slide">
								<img src="https://picsum.photos/640/480?random" alt="slide 2">
							</div>
							<div class="swiper-slide">
								<img src="https://picsum.photos/640/480?random" alt="slide 3">
							</div>
						</div>
						<div class="swiper-pagination"></div> 
						<div class="swiper-button-prev"></div>
						<div class="swiper-button-next"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<section class="section-container">
	<div class="section-inner">
		<div class="row">
			<div class="columns small-12">
				<?php
				Grav\WP\Content::get_template_part(
					'components/entry',
					array(
						'post_id' => 2
					)
				);
				?>
			</div>
		</div>
	</div>
</section>
