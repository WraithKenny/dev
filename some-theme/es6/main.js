import jQuery from 'jquery';

// To test @babel/runtime, uncomment below.
// 'foobar'.includes( 'foo' );

// To test @babel/env, uncomment below.
// Array.from( new Set( [ 1, 2, 3, 2, 1 ] ) ); // => [1, 2, 3]
// [ 1, [ 2, 3 ], [ 4, [ 5 ] ] ].flat( 2 ); // => [1, 2, 3, 4, 5]
// Promise.resolve( 32 ).then( ( x ) => console.log( x ) ); // => 32

jQuery( function( $ ) {
	// Will use core's version in production.
	console.log( $().jquery );
} );
