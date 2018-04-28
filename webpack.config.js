const path 				= require('path');
// const UglifyJsPlugin 	= require('uglifyjs-webpack-plugin');

module.exports = {
	mode: 'production',
	cache: true,
	devtool: 'source-map',
	entry: './js/master.js',
	output: {
		path: path.join(__dirname, '/dist/js'),
		filename: 'master.min.js'
	},
	resolve: {
		modules: ['node_modules']
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules\/(?!(dom7|swiper)\/).*/,
				use: {
					loader: 'babel-loader',
					options: {
						// presets: [
						// 	['babel-preset-env', {
						// 		targets: {
						// 			browsers: ['last 2 versions', 'ie >= 11']
						// 		}
						// 	}]
						// ],
						cacheDirectory: true
					}
				}
			}
		]
	}
	// plugins: [
	// 	new UglifyJsPlugin({
	// 		// exclude: /(node_modules|bower_components)/,
	// 		// exclude: /node_modules\/(?!(dom7|swiper)\/).*/,
	// 		exclude: /node_modules/,
	// 		cache: true,
	// 		parallel: true,
	// 		uglifyOptions: {
	// 			warnings: true,
	// 			output: {
	// 				comments: false,
	// 				beautify: false
	// 			}
	// 		},
	// 		sourceMap: true
	// 	})
	// ]
};
