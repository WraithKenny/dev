import webpack from 'webpack';
import config from './config/prod.mjs';

const compiler = webpack( config );

export default function compileJs( cb ) {
	compiler.run( () => {
		cb();
	} );
}
