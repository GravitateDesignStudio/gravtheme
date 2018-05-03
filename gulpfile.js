function loadLocalConfig() {
	const fs = require('fs');

	try {
		return JSON.parse(fs.readFileSync('local_config.json'));
	} catch (e) {
		return false;
	}
}

const gulp			= require('gulp');
const plugins		= require('gulp-load-plugins')();
const browsersync	= require('browser-sync').create();
const webpack		= require('webpack');
const webpackStream	= require('webpack-stream');
const webpackConfig	= require('./webpack.config.js');

gulp.task('scss', function () {
	return gulp.src('css/master.scss')
		.pipe(plugins.sourcemaps.init())
		.pipe(plugins.sass())
		.on('error', plugins.sass.logError)
		.pipe(plugins.autoprefixer({
			browsers: ['last 2 versions', 'ie >= 11']
		}))
		.pipe(plugins.cleanCss({
			keepSpecialComments: 0
		}))
		.pipe(plugins.rename('master.min.css'))
		.pipe(plugins.sourcemaps.write('.'))
		.pipe(gulp.dest('dist/css'))
		.pipe(browsersync.stream())
		.on('error', plugins.notify.onError({
			message: '<%= error.message %>',
			title: 'SCSS Error'
		}));
});

gulp.task('scss-editor-styles', function () {
	return gulp.src('css/editor-styles.scss')
		.pipe(plugins.sourcemaps.init())
		.pipe(plugins.sass())
		.on('error', plugins.sass.logError)
		.pipe(plugins.autoprefixer({
			browsers: ['last 2 versions', 'ie >= 11']
		}))
		.pipe(plugins.cleanCss({
			keepSpecialComments: 0
		}))
		.pipe(plugins.rename('editor-styles.min.css'))
		.pipe(plugins.sourcemaps.write('.'))
		.pipe(gulp.dest('dist/css'))
		.on('error', plugins.notify.onError({
			message: '<%= error.message %>',
			title: 'SCSS (editor styles) Error'
		}));
});

gulp.task('js', function () {
	return gulp.src('js/master.js')
		.pipe(plugins.plumber({
			errorHandler: function (err) {
				plugins.notify.onError({
					title: 'JS Build Error',
					message: err.message
				})(err);
			}
		}))
		.pipe(plugins.notify({
			message: 'Starting JS build',
			title: 'JS Build'
		}))
		.pipe(webpackStream(webpackConfig, webpack))
		.pipe(gulp.dest('dist/js'));
});

gulp.task('js-watch', ['js'], function (done) {
	browsersync.reload();
	done();
});

gulp.task('browser-sync', function () {
	const localConfig = loadLocalConfig();

	if (!localConfig) {
		console.error('Unable to load "local_config.json" -- please use "local_config_example.json" as a template');
		process.exit();
	}

	browsersync.init(localConfig.browserSync);

	gulp.watch('css/**/*.scss', ['scss']);
	gulp.watch('js/**/*.js', ['js-watch']);
	gulp.watch('**/*.php').on('change', browsersync.reload);
});

gulp.task('default', ['scss', 'js', 'browser-sync'], function () {
	const localConfig = loadLocalConfig();

	if (!localConfig) {
		console.error('Unable to load "local_config.json" -- please use "local_config_example.json" as a template');
		process.exit();
	}
});
