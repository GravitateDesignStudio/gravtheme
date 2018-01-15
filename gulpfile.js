const gulp			= require('gulp');
const plugins		= require('gulp-load-plugins')();
const browsersync	= require('browser-sync').create();
const webpack		= require('webpack');
const webpackStream	= require('webpack-stream');
const webpackConfig	= require('./webpack.config.js');

// gulp.task('scss', function () {
// 	return gulp.src('css/master.scss')
// 		.pipe(plugins.sourcemaps.init())
// 		.pipe(plugins.sass())
// 		.on('error', plugins.sass.logError)
// 		.pipe(plugins.autoprefixer({
// 			browsers: ['last 2 versions', 'ie >= 11']
// 		}))
// 		.pipe(plugins.cleanCss({
// 			keepSpecialComments: 0
// 		}))
// 		.pipe(plugins.rename('master.min.css'))
// 		.pipe(plugins.sourcemaps.write('.'))
// 		.pipe(gulp.dest('dist/css'));
// });

// // gulp.task('scss-editor-styles', function () {
// // 	return gulp.src('assets/css/editor-styles.scss');
// // });

// gulp.task('js', function () {
// 	return gulp.src('js/master.js')
// 		.pipe(webpackStream(webpackConfig, webpack))
// 		.pipe(gulp.dest('dist/js'));
// });

// gulp.task('browser-sync', function () {
// 	browsersync.init({
// 		proxy: 'gravtheme.local.com',
// 		files: [
// 			'dist/css/master.min.css',
// 			'dist/js/master.min.js',
// 			'**/*.php'
// 		],
// 		open: false,
// 		notify: true,
// 		https: false
// 	});
// });

// gulp.task('default', ['scss', 'js', 'browser-sync'], function () {
// 	gulp.watch('css/**/*.scss', ['scss']);
// 	gulp.watch('js/**/*.js', ['js']);
// });

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
		.pipe(browsersync.stream());
});

// gulp.task('scss-watch', ['scss'], function (done) {
// 	browsersync.reload();
// 	done();
// });

// gulp.task('scss-editor-styles', function () {
// 	return gulp.src('assets/css/editor-styles.scss');
// });

gulp.task('js', function () {
	return gulp.src('js/master.js')
		.pipe(webpackStream(webpackConfig, webpack))
		.pipe(gulp.dest('dist/js'));
});

gulp.task('js-watch', ['js'], function (done) {
	browsersync.reload();
	done();
});

gulp.task('browser-sync', function () {
	browsersync.init({
		proxy: 'gravtheme.local.com',
		// files: [
		// 	'dist/css/master.min.css',
		// 	'dist/js/master.min.js',
		// 	'**/*.php'
		// ],
		open: false,
		notify: true,
		https: false
	});

	gulp.watch('css/**/*.scss', ['scss']);
	gulp.watch('js/**/*.js', ['js-watch']);
	gulp.watch('**/*.php').on('change', browsersync.reload);
});

gulp.task('default', ['scss', 'js', 'browser-sync'], function () {
	// gulp.watch('css/**/*.scss', ['scss-watch']);
	// gulp.watch('js/**/*.js', ['js-watch']);
	// gulp.watch('**/*.php').on('change', browsersync.reload);
});
