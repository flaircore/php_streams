const MiniCSSExtractPlugin = require("mini-css-extract-plugin")
const path = require("path")

let mode = "development",
    source_map = "source-map"

// if NODE_ENV is set to prod, we disable source-maps,
// and set webpack mode is production for it to use
// its built in optimizations accordingly eg minified/optimized
// files.
if (process.env.NODE_ENV === "production") {
    mode = "production"
    source_map = "eval"
}

module.exports = {
    mode: mode,
    target: ["web", 'es5'],
    /**
     * entries for raw js files (source)
     */
    entry: {
        main: path.resolve(__dirname, 'src_js/main.js'),
    },
    /**
     * output folder,
     * where [name] === entry[name]/entry[i] from above
     */
    output: {
        filename: '[name].bundle.js',
        path: path.resolve(__dirname, 'dist'),
        // clean: true; cleans the output folder from previous builds.
        clean: true,
    },

    /**
     * devtools controls if and how source maps are generated.
     */
    devtool: source_map,

    /**
     * https://webpack.js.org/configuration/plugins/
     */
    plugins: [
        new MiniCSSExtractPlugin()
    ],

    /**
     * https://webpack.js.org/configuration/module/#rule
     */
    module: {
        rules: [
            {
                test: /\.(sc|c)ss$/i,
                /**
                 * postcss-loader (postcss.config.js),
                 * css-loader and
                 * finally we extract css to
                 * a separate file with MiniCSSExtractPlugin.loader plugin.
                 * Another option, is to use style-loader to inject inline css into
                 * our template files but we don't need that approach.
                 */
                use:[
                    MiniCSSExtractPlugin.loader,
                    "css-loader",
                    /**
                     * postcss-loader (postcss.config.js)
                     */
                    "postcss-loader",
                    "sass-loader"
                ]
            },
            {
                test: /\.js$/i,
                exclude: /node_modules/,
                /**
                 * babel-loader (babel.config.js)
                 */
                use: [
                    "babel-loader"
                ]
            },
            {
                test: /\.(png|jpe?g|gif|svg)$/i,
                type: "asset"
            },
        ]
    },
}
