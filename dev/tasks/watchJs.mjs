import process from 'process';
import gulp from 'gulp';
import webpack from 'webpack';
import server from './util/server.mjs';
import s from './util/settings.mjs';
import config from './config/dev.mjs';

const cwd = process.cwd();

const compiler = webpack( config );

function compileJsDev( cb ) {
	compiler.run( () => {
		server.reload();
		cb();
	} );
}

export default function watchJs( cb ) {
	gulp.watch( s.folder + '/es6/**/*.{js,mjs}', { cwd }, compileJsDev );
	cb();
}
