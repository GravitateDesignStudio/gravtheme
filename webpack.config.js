const path 				= require('path');
const UglifyJsPlugin 	= require('uglifyjs-webpack-plugin');

module.exports = {
	// watch: true,
	cache: true,
	devtool: 'cheap-source-map',
	entry: './js/master.js',
	output: {
		path: path.join(__dirname, '/dist/js'),
		filename: 'master.min.js'
	},
	resolve: {
		modules: ['node_modules']
	},
	module: {
		loaders: [
			{
				test: /\.js$/,
				exclude: /(node_modules|bower_components)/,
				// exclude: /node_modules\/(?!(dom7|swiper)\/).*/,
				loader: 'babel-loader?cacheDirectory=true',
				query: {
					presets: [
						['babel-preset-env', {
							targets: {
								browsers: ['last 2 versions', 'ie >= 11']
							}
						}]
					]
				}
			}
		]
	},
	plugins: [
		new UglifyJsPlugin({
			exclude: /(node_modules|bower_components)/,
			// exclude: /node_modules\/(?!(dom7|swiper)\/).*/,
			cache: true,
			parallel: true,
			uglifyOptions: {
				warnings: true
			}
		})
	]
};
