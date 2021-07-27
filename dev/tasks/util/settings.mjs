import process from 'process';
import path from 'path';
import conf from 'pkg-conf';

const dev = conf.sync( 'dev' );
if ( ! dev.hasOwnProperty( 'folder' ) ) {
	console.error( 'Error: Please set a `dev.folder` in your `package.json`.' );
	process.exit(0);
}

const folder = path.normalize( dev.folder );
const domain = dev.domain;

const sass = {};

if ( dev?.sass?.src ) {
	sass.src = path.join( folder, dev.sass.src, '**/*.{scss,sass}' );
} else {
	sass.src = path.join( folder, 'sass', '**/*.{scss,sass}' );
}

if ( dev?.sass?.dest ) {
	sass.dest = path.join( folder, dev.sass.dest );
} else {
	sass.dest = path.join( folder, 'css' );
}

const js = {
	dest: '',
	src: '',
};

const settings = {
	folder,
	domain,
	sass,
	js
};

export default settings;
