/*
 |--------------------------------------------------------------------------
 | Setup
 |--------------------------------------------------------------------------
 */
	// Node Modules
	const RoutesVersioning = require( 'express-routes-versioning' )();

	// Controllers
	const Controllers = {
		v_1_0: {
			Afdeling: require( _directory_base + '/app/v1.0/Http/Controllers/AfdelingController.js' ),
			Block: require( _directory_base + '/app/v1.0/Http/Controllers/BlockController.js' ),
			Comp: require( _directory_base + '/app/v1.0/Http/Controllers/CompController.js' ),
			Est: require( _directory_base + '/app/v1.0/Http/Controllers/EstController.js' ),
			LandUse: require( _directory_base + '/app/v1.0/Http/Controllers/LandUseController.js' ),
			Region: require( _directory_base + '/app/v1.0/Http/Controllers/RegionController.js' ),
		}
	}

	// Middleware
	const Middleware = {
		v_1_0: {
			VerifyToken: require( _directory_base + '/app/v1.0/Http/Middleware/VerifyToken.js' )
		}
	}
	
/*
 |--------------------------------------------------------------------------
 | Routing
 |--------------------------------------------------------------------------
 */
	module.exports = ( app ) => {

		/*
		 |--------------------------------------------------------------------------
		 | Welcome Message
		 |--------------------------------------------------------------------------
		 */
			app.get( '/', ( req, res ) => {
				res.json( { 
					application: {
						name : config.app.name,
						port : config.app.port,
						environment : config.app.env
					} 
				} )
			} );

		/*
		 |--------------------------------------------------------------------------
		 | API Versi 1.0
		 |--------------------------------------------------------------------------
		 */
		/*
		 |--------------------------------------------------------------------------
		 | Old API
		 |--------------------------------------------------------------------------
		 */

		 	// Afdeling
		 	//app.get( '/afdeling/all', token_verify, AfdelingController.findAll );
			//app.get( '/afdeling/q', token_verify, AfdelingController.findAll );
			//app.post( '/sync-tap/afdeling', AfdelingController.createOrUpdate );
			app.get( '/sync-mobile/afdeling/:start_date/:end_date', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Afdeling.sync_mobile );
			app.get( '/afdeling', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Afdeling.find );
			//app.post( '/afdeling', AfdelingController.create );
			//app.get( '/afdeling/:id', AfdelingController.findOne );
			//app.put( '/afdeling/:id', AfdelingController.update );
			//app.delete( '/afdeling/:id', AfdelingController.delete );

			// Block
			//app.get( '/block/all', token_verify, BlockController.findAll );
			//app.get( '/block/q', token_verify, BlockController.findAll );
			//app.post( '/sync-tap/block', BlockController.createOrUpdate );
			app.get( '/sync-mobile/block/:start_date/:end_date', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Block.sync_mobile );
			app.get( '/block', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Block.find );
			//app.post( '/block', BlockController.create );
			//app.get( '/block/:id', BlockController.findOne );
			//app.put( '/block/:id', BlockController.update );
			//app.delete( '/block/:id', BlockController.delete );
			app.get( '/geom/design/block/:id', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Block.find_one_geom );

			// Comp
			//app.get( '/comp/all', token_verify, CompController.findAll );
			//app.get( '/comp/q', token_verify, CompController.findAll );
			//app.post( '/sync-tap/comp', token_verify, CompController.createOrUpdate );
			app.get( '/sync-mobile/comp/:start_date/:end_date', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Comp.sync_mobile );
			//app.delete( '/comp/:id', token_verify, CompController.delete );
			app.get( '/comp', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Comp.find );
			//app.post( '/comp', CompController.create );
			//app.get( '/comp/:id', CompController.findOne );
			//app.put( '/comp/:id', CompController.update );

			// Est
			//app.get( '/est/all', token_verify, EstController.findAll );
			//app.get( '/est/q', token_verify, EstController.findAll );
			//app.post( '/sync-tap/est', verifyToken, EstController.createOrUpdate );
			app.get( '/sync-mobile/est/:start_date/:end_date', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Est.sync_mobile );
			app.get( '/est', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Est.find );
			//app.post( '/est', EstController.create );
			//app.get( '/est/:id', EstController.findOne );
			//app.put( '/est/:id', EstController.update );
			//app.delete( '/est/:id', EstController.delete );

			// Land Use
			//app.get( '/land-use/all', token_verify, LandUseController.findAll );
			//app.get( '/land-use/q', token_verify, LandUseController.findAll );
			//app.get( '/report/land-use/:id', token_verify, LandUseController.findOneForReport );
			//app.get( '/land-use/q', token_verify, LandUseController.findAll );
			app.get( '/land-use', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.LandUse.find );
			//app.post( '/sync-tap/land-use', token_verify, LandUseController.createOrUpdate );
			app.get( '/sync-mobile/land-use/:start_date/:end_date', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.LandUse.sync_mobile );

		 	// Region
			//app.get( '/region/all', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Region.findAll );
			//app.get( '/region/q', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Region.findAll );
			//app.post( '/sync-tap/region', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Region.createOrUpdate );
			app.get( '/sync-mobile/region/:start_date/:end_date', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Region.sync_mobile );
			//app.post( '/region', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Region.create );
			app.get( '/region', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Region.find );
			//app.get( '/region/:id', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Region.findOne );
			//app.put( '/region/:id', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Region.update );
			//app.delete( '/region/:id', Middleware.v_1_0.VerifyToken, Controllers.v_1_0.Region.delete );

	}