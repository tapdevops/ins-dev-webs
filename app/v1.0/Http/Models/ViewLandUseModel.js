/*
 |--------------------------------------------------------------------------
 | Variable
 |--------------------------------------------------------------------------
 */
	const mongoose = require( 'mongoose' );

/*
 |--------------------------------------------------------------------------
 | Schema
 |--------------------------------------------------------------------------
 */
	const ViewLandUseSchema = mongoose.Schema( {});

/*
|--------------------------------------------------------------------------
| Module Exports
|--------------------------------------------------------------------------
*/
	module.exports = mongoose.model( 'ViewLandUse', ViewLandUseSchema, 'VIEW_LAND_USE' );