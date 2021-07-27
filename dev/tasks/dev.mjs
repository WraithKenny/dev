import gulp from 'gulp';
import serve from './serve.mjs';
import watchPhp from './watchPhp.mjs';
import watchSass from './watchSass.mjs';
import watchJs from './watchJs.mjs';

export default gulp.series( serve, gulp.parallel( watchPhp, watchJs, watchSass ) );
