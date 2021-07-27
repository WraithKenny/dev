import gulp from 'gulp';
import compileSass from './compileSass.mjs';
import compileJs from './compileJs.mjs';

export default gulp.parallel( compileJs, compileSass );
