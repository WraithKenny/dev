import gulpSassC from 'gulp-sass';
import sass from 'sass';

const gulpSass = gulpSassC( sass );

// Sync is faster than Async, and Fibers is obsolete.
const pipeSass = () =>
	gulpSass
		.sync( {
			includePaths: [ 'node_modules' ],
		} )
		.on( 'error', gulpSass.logError );

export default pipeSass;
