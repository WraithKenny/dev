import process from 'process';
import gulp from 'gulp';
import server from './util/server.mjs';
import s from './util/settings.mjs';

const cwd = process.cwd();

export default function watchPhp( cb ) {
	gulp.watch( s.folder + '/**/*.php', { cwd }, function reload( rcb ) {
		server.reload();
		rcb();
	} );
	cb();
}
