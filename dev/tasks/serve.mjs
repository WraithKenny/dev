import server from './util/server.mjs';
import s from './util/settings.mjs';

export default function serve( cb ) {
	server.init(
		{
			proxy: 'https://' + s.domain + '.test',
			host: s.domain + '.test',
			open: 'external',
			ghostMode: false,
			logLevel: 'info',
			https: {
				key: './dev/files/ssl/localhost.key',
				cert: './dev/files/ssl/localhost.crt',
			},
		},
		cb
	);
}
