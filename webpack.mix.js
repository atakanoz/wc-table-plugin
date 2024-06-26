const mix = require('laravel-mix');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');

mix
  .setPublicPath('./');

/** ---------------------------------------------------
 * Admin Assets
 * Inc. styles, autoprefixer configs and static assets.
 ----------------------------------------------------*/
mix
  .sass(
    'resources/admin/source/styles/main.scss',
    'resources/admin/dist/styles.bundle.css',
    { sassOptions: { outputStyle: 'compressed' } }
  )
  .options({
    postCss: [
      require('css-declaration-sorter')({
        order: 'smacss'
      })
    ],
    autoprefixer: {
      options: {
        browsers: [
          'last 6 versions',
        ]
      }
    }
  });

/** ---------------------------------------------------
 * Public Assets
 * Inc. styles, autoprefixer configs and static assets.
 ----------------------------------------------------*/
mix
  .sass(
    'resources/public/source/styles/main.scss',
    'resources/public/dist/styles.bundle.css',
    { sassOptions: { outputStyle: 'compressed' } }
  )
  .options({
    postCss: [
      require('css-declaration-sorter')({
        order: 'smacss'
      })
    ],
    autoprefixer: {
      options: {
        browsers: [
          'last 6 versions',
        ]
      }
    }
  });

/** ---------------------------------------------------
 * Options
 * Post CSS and autoprefixer options.
 ----------------------------------------------------*/
mix
  .options({
    processCssUrls: false,
    postCss: [
      require('postcss-nested-ancestors'),
      require('postcss-nested'),
      require('postcss-import'),
      require('tailwindcss'),
      require('autoprefixer'),
    ]
  });

/** ---------------------------------------------------
 * Webpack Config
 * Custom webpack config block.
 ----------------------------------------------------*/
mix
  .webpackConfig({
    plugins: [
      // new CopyWebpackPlugin({
      //   patterns: [
      //     { from: "resources/admin/source/images", to: "images" },
      //     { from: "source/icons", to: "icons" },
      //     { from: "source/fonts", to: "fonts" },
      //   ],
      // }),
      new StyleLintPlugin({
        files: './source/styles/**/*.scss',
        configFile: './.stylelintrc'
      }),
    ]
  });

/** ---------------------------------------------------
 * Browsersync
 ----------------------------------------------------*/
mix
  .browserSync({
    proxy: 'http://wp.local',
    open: 'external',
    port: 3000,
    files: [
      '*.php',
      'admin/**/*.php',
      'public/**/*.php',
      'includes/**/*.php',
      'resources/**/**/*',
    ]
  });

/** ---------------------------------------------------
 * Extras
 ----------------------------------------------------*/
mix
  .disableNotifications();

mix
  .version();
