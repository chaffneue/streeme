/*
* The Streeme Class is a javascript library for php and javascript intercommunication 
* with the streeme objects and templates
*/

//Test if JQuery is Loaded 
if ( typeof $ == 'undefined' )
{
		throw ( new Error (
			"\n\n" +
			"This Application Requires the JQuery javascript framework" +
			"\n\n"
		)); 
}

//Create the Streeme Class
if ( typeof streeme == 'undefined' ){ streeme = {} }

streeme = {
	/**
	* the current song id being played
	*/
	songPointer : 0,
	
	/**
	* the index position in the table of the current song being played
	*/
	displayPointer : 0,
	
	/**
	* the id of the song that's currently playing
	*/
	currentSongId : null,
	
	/**
	* set pagination length for the song dataTable
	*/
	iDisplayLength : 50,
	
	/**
	* Random / shuffling flag bool: true or false;
	*/
	random : false,
	
	/**
	* Record the connection speed in kb/s around when the application loads
	*/
	connectionSpeed : 0,
	
	/**
	* If greater than zero, Streeme will degrade quality of MP3s to the set bitrate
	*/
	bitrate : 0,
	
	/**
	* If not false, Streme will modify the target file format of Media to the set format
	*/
	format : false,

	/**
	 * play or pause state
	 */
	play : true,
	
	/**
	* The song id that is queued to play next
	*/
	queuedSongId : false,
	
	/**
	* store the state of the cards in this interface
	*/
	cancelnavigation : false,
	oldstate : null,
	newstate : null,
	
	/**
	* Store the user's filters
	*/
	artist_id : false,
	album_id : false,
	song_id : false,
	genre_id : false,
	playlist_id : false,
	keywords : false,
	shuffle : false,
	
	/**
	* Store the user's last alpha states
	*/
	artist_alpha : 'a',
	album_alpha : 'a',
	song_alpha : 'a',
	genre_alpha : 'all',
	playlist_alpha: 'all',
	hideSongAlpha : false, //temporarily hide song alpha bar
	
	keywords : null,
	
	/**
	 * Send cookies to the media player for audio players without cookie functionality (mobile)
	 */
	send_session_cookies : false,
	send_cookie_name : null,
	
	/**
	 * Variables for the resume functionality
	 */
	timer : 0,
	rSongId : 0, 
	rSongName : null,
	rAlbumName : null,
	rArtistName : null,
	
	/**
	 * The play duration of the current song
	 */
	totalPlayTime : 0,
	
	/**
	* initialize the application - project constructor
	* sets up the datatable object for songs and other general setup
	*/
	__initialize : function( results_per_page ) 
	{
		/**************************************************
		* Load the Datatable                              *
		***************************************************/
		streeme.iDisplayLength = results_per_page;

		$('#songlist').dataTable
		( 
			{
				/* datatable config */
				"bProcessing"     : true,
				"bServerSide"     : true,
				"sAjaxSource"     : javascript_base + '/service/listSongs',
				"iDisplayLength"  : streeme.iDisplayLength,
				"bJQueryUI"       : true,
				"sPaginationType" : "full_numbers",
				"bAutoWidth"      : false,
				"bStateSave"      : true, 
				/* localize the datatable UI */
				"oLanguage" :
				{
					"oPaginate":
					{
						"sFirst"        : sFirst,
						"sLast"         : sLast,
						"sNext"         : sNext,
						"sPrevious"     : sPrevious
					},
					"sEmptyTable"   : sEmptyTable,
					"sInfo"         : sInfo,
					"sInfoEmpty"    : sInfoEmpty,
					"sInfoFiltered" : sInfoFiltered,
					"sLengthMenu"   : sLengthMenu,
					"sProcessing"   : sProcessing,
					"sSearch"       : sSearch,
					"sZeroRecords"  : sZeroRecords,
				},		
				/* hide the song id field from the user */
				"aoColumns" : 
				[ 
					{ "sClass" : "song_id" },
					{ "sClass" : "song_tbl_title" },
					{ "sClass" : "hidden" },
					{ "sClass" : "hidden" },
					{ "sClass" : "hidden" },
					{ "sClass" : "hidden" },
					{ "sClass" : "hidden" },
					{ "sClass" : "hidden" },
					{ "sClass" : "hidden" },
				],
				/* default sorting by newest songs */
				"aaSorting": [[ 4, "desc" ]],
							
				"fnRowCallback" : function( nRow, aData, iDisplayIndex )
				{
					/* let each row doubleclick load the next song */
					$( nRow ).click
					(
						function()
						{						  
							//play the song
							streeme.playSong( aData[0], aData[1], aData[2], aData[3], 0 );
							  
							//update the class pointers
							streeme.songPointer = aData[0];
							streeme.displayPointer = iDisplayIndex;
						}
					);
					
					$( nRow ).attr( "id", "sltr" + aData[0] );
					return nRow;
				},
				
				"fnDrawCallback" : function()
				{
					//highlight the currently playing song 
					streeme.retarget();
				},
				
				"fnCookieCallback": function( sName, oData, sExpires, sPath)
				{
					//extend the state save cookies to 300 days
					var newdate = new Date();
					newdate.setTime(newdate.getTime()+(300*24*60*60*1000));
					var expires = newdate.toGMTString();
					
					// Choose what to save in cookie and what not to
					if (sName != 'sFilter')
				  	{
					  	return sName + "=" + JSON.stringify(oData) + "; expires=" + expires + "; path=" + sPath;
				  	}
				}
			}
		);	
	
	
		/**************************************************		
		* Read and setup cookie based ui  settings       *
		**************************************************/
		if( $.cookie('modify_bitrate') )
		{
			if( $( '#bitrateselector' ) )
			{
				$( '#bitrateselector' ).val( $.cookie( 'modify_bitrate' ) );
			}
			streeme.bitrate = $.cookie( 'modify_bitrate' );
			if( streeme.bitrate == 'auto' )
			{
				streeme.getConnectionSpeed();
			}
		}
		
		if( $.cookie('modify_format') )
		{
			if( $( '#formatselector' ) )
			{
				$( '#formatselector' ).val( $.cookie( 'modify_format' ) );
			}
			streeme.format = $.cookie( 'modify_format' );
		}
		
		/**************************************************		
		* Load Lists                                      *
		**************************************************/
		streeme.getList( 'artist' );
		streeme.getList( 'album' );
		streeme.getList( 'genre' );
		streeme.getList( 'playlist' );		
		
		/**************************************************		
		 * Register Event listeners for the Streeme class *
		 **************************************************/
		/* Configure the HTML5 Audio/Video Tag listeners */
		if( $( '#musicplayer' ) )
		{
			//the song unloaded normally
			$( '#musicplayer' ).bind( 'ended', function()
				{
					if( parseInt( streeme.timer ) > 0 )
					{
						streeme.playNextSong();
					}
				}
			);
			
			//check play/paused state
			$( '#musicplayer' ).bind( 'pause', function(event)
				{
					//this handles a strange bug with streams under iOS 4.3 
					//where the UI will mysteriously pause instead of going to the next song
					if( parseInt( streeme.timer ) >= ( streeme.totalPlayTime-10 ) )
					{
						streeme.playNextSong();
					}
					else
					{	
						streeme.play=false;
					}
				} 
			);
			$( '#musicplayer' ).bind( 'play', function(event){ streeme.play=true } );			
			
			//check for seeking
			$( '#musicplayer' ).bind( 'seeked', function(event){ streeme.timer = Math.floor( this.currentTime ) } );
			
			//The file was not the size reported or the codec is missing 
			$( '#musicplayer' ).bind( 'error', function(){ if( this.error.code == 4 ) streeme.playNextSong(); } );
		}
		
		/* Button listeners */ 
		if( $( '#next' ) )
		{
			$( '#next' ).click( streeme.playNextSong );
		}
		if( $( '#previous' ) )
		{
			$( '#previous' ).click( streeme.playPreviousSong );
		}
		if( $( '#albumart' ) )
		{
			$( '#albumart' ).click( streeme.playPause );
		}
		if( $( '#logout' ) )
		{
			$( '#logout' ).click( streeme.logout );
		}
		if( $( '#bitrateselector' ) )
		{
			$( '#bitrateselector' ).change( streeme.updateBitrate );
			$( '#bitrateselector' ).keyup( streeme.updateBitrate );
		}
		if( $( '#formatselector' ) )
		{
			$( '#formatselector' ).change( streeme.updateFormat );
			$( '#formatselector' ).keyup( streeme.updateFormat );
		}
		if( $( '#genreselector' ) )
		{
			$( '#genreselector' ).change( streeme.chooseGenre );
			$( '#genreselector' ).keyup( streeme.chooseGenre );
		}
		if( $( '#cancelsearch' ) )
		{
			$( '#cancelsearch' ).click( streeme.clearSearch );
		}
		if( $( '#addplaylist' ) )
		{
			$( '#addplaylist' ).click( streeme.addPlaylist );
		}
		
		/**************************************************		
		* Run initial setup scripts                      *
		**************************************************/
		streeme.chooseState( 'xiby20100s', 'card_welcome' );
		streeme.hideNavigationBar();
		streeme.updateSongMenu();
		streeme.send_session_cookies = send_session_cookies;
		streeme.send_cookie_name = send_cookie_name;
		
		/**************************************************		
		* Register event timers                          *
		**************************************************/
		setInterval( streeme.playSongInQueue, 1000 );
	},

	/**
	* User has chosen a track to play from the songlist, update the player
	* @param song_id 		int: database id for the song 
	* @param song_name 		str: name of the song
	* @param album_name 	str: name of the album to which this song belongs
	* @param artist_name 	str: name of the artist to which this song belongs
	* @param time_offset    int: the start time offset in seconds
	*/
	playSong : function( song_id, song_name, album_name, artist_name, time_offset )
	{
		//start the track from a specific offset if given
		if( time_offset > 0 )
		{
			streeme.timer = time_offset;
		}
		else
		{
			streeme.timer = 0;
		}
		
		//queue up the song for the next play cycle
		streeme.queuedSongId = song_id;
		
		//switch to player card 
		streeme.chooseState( 'card_songs', 'card_player');
		
		//remove previous song tr cursor
		$( 'table.songlist tbody tr.nowplaying' ).removeClass( 'nowplaying' );
		
		if( $( '#sltr' + song_id ) )
		{
			//update playing song tr cursor
			$( '#sltr' + song_id ).addClass( 'nowplaying' ); 

			//update the songname
			if( $( '#songtitle' ) )
			{
				$( '#songtitle' ).text( artist_name + ' - ' + streeme.stripTags( song_name ) ); 
			}

			//add album art to the viewer
			if( $( '#albumart' ) && ( artist_name != null || album_name != null ) )
			{
				$( '#albumart' ).html( '<div id="pauseoverlay"></div><img src="' + javascript_base + '/art/' + $.md5( artist_name + album_name ) + '/large" alt="' + sAlbumArtImageAlt + ' ' + album_name + '" class="albumimg" border="0"/>' );
			}
			else
			{
				$( '#albumart' ).html( '<div id="pauseoverlay"></div><img src="' + javascript_base + '/art/placeholder/large" alt="' + sAlbumArtImageAlt + ' ' + album_name + '" class="albumimg" border="0"/>' );				
			}

			//update next previous buttons
			if( $( '#next' ) )
			{
				$( '#next' ).addClass( 'nextsong' );
				$( '#next' ).removeClass( 'nextsongdisabled' );
			}
			if( $( '#previous' ) )
			{
				$( '#previous' ).addClass( 'previoussong' );
				$( '#previous' ).removeClass( 'previoussongdisabled' );
			}
		}
		
		// set the resume information
		streeme.rSongId = song_id; 
		streeme.rSongName = streeme.stripTags( song_name );
		streeme.rAlbumName = album_name;
		streeme.rArtistName = artist_name;
		
		// set the song pointer for the playback cursor
		streeme.songPointer = song_id;
		
		//get the expected play time in seconds
		var playtimes =  $('#sltr' + streeme.songPointer ).children( ':eq(6)' ).text().split(':');
		if(playtimes.length > 1)
		{ 
			streeme.totalPlayTime = ( parseInt( playtimes[0] ) * 60 ) + parseInt( playtimes[1] );
		} 
		else
		{
			streeme.totalPlayTime = parseInt( playtimes[0] );
		}
	},
	
	/**
	* Some browsers get unstable if send too many play requests through the javascript API
	* poll this at the fastest rate possible before the browser begins to fail
	*/
	playSongInQueue : function()
	{
		if( streeme.queuedSongId != false )
		{
			//build HTTP request	
			var parameters = new Array();
			if( streeme.bitrate != 0 )
			{
				parameters.push( 'target_bitrate=' + streeme.bitrate ); 
			}
			if( streeme.format != false )
			{
				parameters.push( 'target_format=' + streeme.format );
			}
			if( streeme.send_session_cookies )
			{
				parameters.push(  streeme.send_cookie_name + '=' + $.cookie( streeme.send_cookie_name ) );
			}	
			if( streeme.timer > 0 )
			{
				parameters.push( 'start_time=' + streeme.timer )
			}
			url = mediaurl + '/play/' + streeme.queuedSongId + '?' + parameters.join('&');
			
			//firefox/chrome logging only 
			//console.log ( url );
			el = document.getElementById( 'musicplayer' );
			el.src = ( url );
			el.preload = 'none';
			el.addEventListener ('canplay', function() { this.play(); });
			el.load();
			
			//clear the queue
			streeme.queuedSongId = false;
		}
		
		//Update the resume cookie if the song is playing
		if( streeme.play == true )
		{
			streeme.timer++;
			
			if( streeme.timer % 2 && streeme.songPointer != 0 )
			{
				var cookie_data = {
						'si' : streeme.rSongId,
						'dp' : streeme.displayPointer,
						't'  : streeme.timer,
						'sn' : streeme.rSongName,
						'an' : streeme.rAlbumName, 
						'rn' : streeme.rArtistName
					};
				$.cookie(
					'resume_mobile', 
					JSON.stringify(cookie_data),
					{ expires : 3000 }
				);
			}
		}
	},
	
	/**
	* Play the next song (in relation to what's playing now) in a given series and ordering from DataTables
	*/
	playNextSong : function()
	{
		try
		{
			var nextSongData = $( '#songlist' ).dataTable().fnGetData( streeme.displayPointer + 1 )
		}
		catch( err )
		{
			//attempt to move to the next page
			try
			{
				if( $( '#songlist_next' ) )
				{
					classList = $( '#songlist_next' ).attr('class').split(' ');
					$.each( classList, function(index, item )
					{
						if(item === 'ui-state-disabled' )
						{
							throw "next page does not exist"; //bit of a hack
							return false;
						}
					});
				}
			
				//move to the next page
				$( '#songlist' ).dataTable().fnPageChange( 'next' );
				
				//let the page redraw and then update the song pointers
				setTimeout(function()
				{ 	
					nextSongData = $( '#songlist' ).dataTable().fnGetData( 0 );
					if( nextSongData )
					{
						streeme.playSong( nextSongData[ 0 ], nextSongData[ 1 ], nextSongData[ 2 ], nextSongData[ 3 ], 0 );
					}
					streeme.displayPointer = 0;
				}
				, 2000);
				
				//scroll back to the top of the container
				if( $( '#songlistcontainer' ) )
				{
					$( '#songlistcontainer' ).scrollTo( 0 );
				}

				//let Javascript get back to work
				return false;
			}
			catch( err )
			{
				if( $( '#next' ) )
				{
				  $( '#next' ).removeClass( 'nextsong' );
					$( '#next' ).addClass( 'nextsongdisabled' );
				}
				//firefox and chrome only
				//console.log ( 'no next songs' );
			} 
		}
		if( nextSongData )
		{
			streeme.playSong( nextSongData[ 0 ], nextSongData[ 1 ], nextSongData[ 2 ], nextSongData[ 3 ], 0 );
			streeme.displayPointer++;
		}
	},

	/**
	* Play the previous song (in relation to what's playing now) in a given series and ordering from DataTables
	*/
	playPreviousSong : function()
	{
		try
		{
			var previousSongData = $( '#songlist' ).dataTable().fnGetData( streeme.displayPointer - 1 )
		}
		catch( err )
		{
			//attempt to move to the previous page
			try
			{
				if( $( '#songlist_previous' ) )
				{
					classList = $( '#songlist_previous' ).attr('class').split(' ');
					$.each( classList, function(index, item )
					{
						if(item === 'ui-state-disabled' )
						{
							throw "previous page does not exist"; //bit of a hack
							return false;
						}
					});
				}

				//move to the previous page
				$( '#songlist' ).dataTable().fnPageChange( 'previous' );
				
				//let the page redraw and then update the song pointers
				setTimeout(function()
				{
					previousSongData = $( '#songlist' ).dataTable().fnGetData( streeme.iDisplayLength - 1	);
					if( previousSongData )
					{
						streeme.playSong( previousSongData[ 0 ], previousSongData[ 1 ], previousSongData[ 2 ], previousSongData[ 3 ], 0 );
					}
					streeme.displayPointer = streeme.iDisplayLength - 1;
				}
				, 2000);

				//scroll back to the top of the container
				if( $( '#songlistcontainer' ) && $( '#songlist' ))
				{
					$( '#songlistcontainer' ).scrollTo( $( '#songlist' ).height() );
				}

				//let Javascript get back to work
				return false;
			}
			catch( err )
			{
				if( $( '#previous' ) )
				{
					$( '#previous' ).removeClass( 'previoussong' );
					$( '#previous' ).addClass( 'previoussongdisabled' );
				}
				//firefox and chrome only
				//console.log ( 'no previous songs' );
			} 
		}
		if( previousSongData )
		{
			streeme.playSong( previousSongData[ 0 ], previousSongData[ 1 ], previousSongData[ 2 ], previousSongData[ 3 ], 0 );
			streeme.displayPointer--;
		}
	},
	
	/**
	 * Play or pause the currently playing song for mobile players with no controls. this is done by clicking the album art image. 
	 * Soon most mobile devices should have much better browser based HTML5 player with native controls. until then, this is a 
	 * workaround so not to duplicate the playback controls symbols.  
	 */
	playPause : function()
	{
		var el = document.getElementById( 'musicplayer' );
		if ( streeme.play )
		{
			el.pause();
			streeme.play = false;
			if( $('#pauseoverlay') )
			{
				$('#pauseoverlay').css({'z-index' : '1'})
			}
		}
		else
		{
			el.play();
			streeme.play = true;
			if( $('#pauseoverlay') )
			{
				$('#pauseoverlay').css({'z-index' : '-1'})
			}
		}
	},
	
	/**
	* Attempt to relocate the current song in the newly drawn matrix and reset the pointers
	*/
	retarget : function()
	{
		//when a user searches and returns to the global view, we need to 
		//retarget the song that's playing on each draw
		try
		{
			var drawData = $( '#songlist' ).dataTable().fnGetData();
			for ( var i; i < drawData.length; i++ )
			{
				if( drawData[i] == streeme.songPointer )
				{
					streeme.displayPointer = i;
				} 
			}
		}
		catch( err )
		{
			return false;
		}
		
		//highlight the current song if it's in view
		if( $( '#sltr' + streeme.songPointer ) )
		{
			$('#sltr' + streeme.songPointer ).addClass( 'nowplaying' )
		}
		 
		//Highlight the header 
		$( '#songlist thead tr th.sorting' ).removeClass('sorting');
		$('span.ui-icon-triangle-1-s').parent().addClass('sorting');
		$('span.ui-icon-triangle-1-n').parent().addClass('sorting');
	},

	/**
	* Search songs, artists and albums 
	* @param keywords str: items to search
	*/
	search : function( type )
	{
		var keyword = prompt( keywordInput, "");
		if( keyword == null ) return false;
		streeme.choose( 'search', keyword );
	},
	
	/**
	* get the equivalent MP3 bitrate for the reported connection speed based on a janky formula:
	* @param speed: int - the returning speed factor from getConnectionSpeed
	* @see  http://techallica.com/kilo-bytes-per-second-vs-kilo-bits-per-second-kbps-vs-kbps
	*/
	setAutoBitrate : function( speed )
	{
		switch ( true ) 
		{
			case ( speed < 15 ):
				streeme.bitrate = 48;
				break;
			case ( speed < 25 ):
				streeme.bitrate = 96;
				break;
			case ( speed < 35 ):
				streeme.bitrate = 128;
				break;
			case ( speed < 40 ):
				streeme.bitrate = 192;
				break;
			case (speed < 45 ):
				streeme.bitrate = 256;
				break;
			case (speed < 55 ):
				streeme.bitrate = 320;
				break;
			case ( speed < 99999999 ):
				streeme.bitrate = 0;
				break;
		}
		//firefox/chrome logging only 
		//console.log ( ( streeme.bitrate != 0 ) ? 'music will be downsampled to: ' + streeme.bitrate + 'k' : 'connection supports all audio speeds - playing original' );
	},
	
	/**
	* Update the bitrate state for the app
	*/
	updateBitrate : function()
	{
		selected_bitrate = $( '#bitrateselector' ).val();
		
		if( selected_bitrate > 0 )
		{
			$.cookie( 'modify_bitrate', selected_bitrate,  { expires: 3000 } );
			streeme.bitrate = selected_bitrate;
		}
		else if( selected_bitrate == 'auto' )
		{
		  $.cookie('modify_bitrate', 'auto', { expires: 3000 } );
			streeme.getConnectionSpeed();
		}
		if( selected_bitrate == 0 )
		{
			$.cookie('modify_bitrate', null );
			streeme.bitrate = 0;
		}
		//firefox/chrome logging only 
		//console.log( streeme.bitrate );
	},

	/**
	* Update the format state for the app
	*/
	updateFormat : function()
	{
		selected_format = $( '#formatselector').val();
		
		if( selected_format != 0 )
		{
			$.cookie('modify_format', selected_format, { expires: 3000 } );
			streeme.format = selected_format;
		}
		if( selected_format == 0 )
		{
			$.cookie('modify_format', null );
			streeme.format = false;
		}
		//firefox/chrome logging only 
		//console.log( streeme.format );
	},

	/*
	* Helper to determine user's connection speed with the server to help improve streaming
	* uses 2 passes with a total page load over head of just under 80kb to test 
	* it's a bit expensive, so leave auto mode off when not required.
	* sets streeme.connectionSpeed;
	*/
	getConnectionSpeed: function()
	{
		var d 		= new Date;
		var start = d.getTime();
		dump 		= $.get( rooturl + '/images/speedtest_bin/st.bin', function()
							{
						    var d = new Date;
						    var time = ( d.getTime() - start ) / 10 / 100;
						    streeme.connectionSpeed = ( 39013 / time / 1000 );
								
								var e = new Date;
						    var estart = e.getTime();
								dump = $.get( rooturl + '/images/speedtest_bin/st.bin', function()
									{
								    var e = new Date;
								    var etime = ( e.getTime() - estart ) / 10 / 100;
								    streeme.connectionSpeed = ( ( 39013 / etime / 1000 ) + streeme.connectionSpeed ) / 2;
								    streeme.setAutoBitrate( streeme.connectionSpeed );
									}
								);
							}
						);
	},
	
	/**
	* Prevent the Navigation bar from showing on webkit mobile
	* devices by scrolling 1px down on page or card refreshes
	*/
	hideNavigationBar : function()
	{
		window.scrollTo(0,1);
	},
	
	/**
	* Change the app's state to a new virtual card. this allows the music to continue playing while you 
	* peruse your library - gives users a solid transition queue when moving to new list content
	* @param newstate str: name of the windw you card you want to switch to.
	*/
	chooseState : function( oldstate, newstate )
	{
		streeme.oldstate = oldstate;
		streeme.newstate = newstate;
		
		$('#' + newstate ).addClass( 'midstack' );
		$('#' + oldstate ).addClass( 'zoomfade' ); 
		setTimeout( function(){ 
								$('#' + oldstate ).removeClass( 'topstack' );
								$('#' + oldstate ).removeClass( 'zoomfade' );
								$('#' + oldstate ).removeClass( 'midstack' );
								$('#' + newstate ).removeClass( 'midstack' );
								$('#' + newstate ).addClass( 'topstack' );	
								streeme.hideNavigationBar();						
							   }, 250 );
	},
	
	
	/**
	* Choose a new alphabetical value for the card 
	* @param listname str: the list name for the active card
	* @param id       str: the id of the item chosen in the active card
	*/
	changeAlpha : function( listname, newalpha )
	{
		switch( listname )
		{
			case 'artist':
				streeme.artist_alpha = newalpha;
				streeme.getList( 'artist' );
				break;
			case 'album':
				streeme.album_alpha = newalpha;
				streeme.getList( 'album' );
				break;
			case 'genre':
				streeme.album_alpha = newalpha;
				streeme.getList( 'genre' );
				break;
			case 'song':
				streeme.song_alpha = newalpha;
				streeme.getList( 'song' );
				break;
			case 'playlist':
				streeme.album_alpha = newalpha;
				streeme.getList( 'playlist' );
				break;
		}
	},
	
	/**
	* Choose a new value for the active card
	* @param listname str: the list name for the active card
	* @param id 			str: the id of the item chosen in the active card
	*/
	choose : function( listname, id )
	{
		switch( listname )
		{
			case 'artist':
				streeme.chooseState( 'card_artists', 'card_albums' );		
				streeme.artist_id = id;
				setTimeout( function() { streeme.getList( 'album' ) }, 600 );
				break;
			case 'album':
				streeme.chooseState( 'card_albums', 'card_songs' );	
				streeme.album_id = id;
				streeme.song_id = false;
				streeme.playlist_id = false;
				streeme.genre_id = false;
				streeme.keywords = false;
				streeme.shuffle = false;
				if( !streeme.album_id && !streeme.artist_id )
				{
					streeme.hideSongAlpha = false;
				}
				else
				{
					streeme.hideSongAlpha = true;
				}
				streeme.getList( 'song' );	
				break;
			case 'genre':
				streeme.chooseState( 'card_genres', 'card_songs' );		
				streeme.genre_id = id;
				streeme.artist_id = false;
				streeme.album_id = false;
				streeme.song_id = false;
				streeme.playlist_id = false;
				streeme.keywords = false;
				streeme.shuffle = false;
				streeme.hideSongAlpha = true;
				streeme.getList( 'song' );
				break;
			case 'playlist':
				streeme.chooseState( 'card_playlists', 'card_songs' );	
				streeme.playlist_id = id;
				streeme.artist_id = false;
				streeme.album_id = false;
				streeme.song_id = false;
				streeme.genre_id = false;
				streeme.keywords = false;
				streeme.shuffle = false;
				streeme.hideSongAlpha = true;
				streeme.getList( 'song' );	
				break;
			case 'search':
				streeme.chooseState( 'xiby20100s', 'card_songs' ); //fade on top
				streeme.keywords = id;
				streeme.playlist_id = false;
				streeme.artist_id = false;
				streeme.album_id = false;
				streeme.song_id = false;
				streeme.genre_id = false;
				streeme.shuffle = false;
				streeme.hideSongAlpha = true;
				streeme.getList( 'song' );	
				break;	
			case 'newest':
				streeme.chooseState( 'card_welcome', 'card_songs' );
				streeme.shuffle = false;
				streeme.keywords = false;
				streeme.playlist_id = false;
				streeme.artist_id = false;
				streeme.album_id = false;
				streeme.song_id = false;
				streeme.genre_id = false;
				streeme.song_alpha = false;
				streeme.hideSongAlpha = true;
				streeme.getList( 'song' );	
				break;	
			case 'shuffle':
				streeme.chooseState( 'card_welcome', 'card_songs' );
				streeme.shuffle = true;
				streeme.keywords = false;
				streeme.playlist_id = false;
				streeme.artist_id = false;
				streeme.album_id = false;
				streeme.song_id = false;
				streeme.genre_id = false;
				streeme.song_alpha = false;
				streeme.hideSongAlpha = true;
				streeme.getList( 'song' );	
				break;
			case 'resume':
				streeme.chooseState( 'card_welcome', 'card_songs' );
				var resume_rawdata = $.cookie('resume_mobile');
				var resume_info = JSON.parse(resume_rawdata);
				//console.log( resume_info );
				streeme.displayPointer = resume_info.dp;
				streeme.playSong( resume_info.si, resume_info.sn, resume_info.an, resume_info.rn, resume_info.t );
				break;		
		}
	},
	
	/** 
	* Get a list from javascript 
	* @param listname		str: artist|album|genre|playlist
	*/
	getList : function( listname )
	{
		var htmlprefix = '';
		
		//build request
		switch( listname )
		{
			case 'artist':
				var listurl = rooturl + '/service/listArtists';
				var listcontainer = $( '#artistlistcontainer' ); 
				htmlprefix += '<li  onclick="streeme.choose( \'' + listname + '\', false )">' + allartists + '</li>';
				var ajaxparameters = ({ 'alpha' : streeme.artist_alpha });
				break;
			case 'album':
				var listurl = rooturl + '/service/listAlbums';
				var listcontainer = $( '#albumlistcontainer' );
				htmlprefix += '<li  onclick="streeme.choose( \'' + listname + '\', false )">' + allalbums + '</li>';
				if ( !streeme.artist_id )
				{
					$( '#card_albums .letterbarcontainer' ).show(); 
				}
				else
				{
					$( '#card_albums .letterbarcontainer' ).hide();
				}
				var ajaxparameters = ( { 'alpha' : ( ( !streeme.artist_id ) ? streeme.album_alpha : 'all' ), 'artist_id' : ( ( !streeme.artist_id ) ? 'all' : streeme.artist_id ) } );
				break;		
			case 'genre':
				var listurl = rooturl + '/service/listGenres';
				var listcontainer = $( '#genrelistcontainer' );
				var ajaxparameters = ( { 'alpha' : streeme.genre_alpha } );
				break;
			case 'playlist':
				var listurl = rooturl + '/service/listPlaylists';
				var listcontainer = $( '#playlistlistcontainer' );
				var ajaxparameters = ( { 'alpha' : streeme.playlist_alpha } );
				break;
			case 'song':
				streeme.getSongData();
				streeme.updateSongMenu();
				if ( streeme.hideSongAlpha )
				{
					$( '#card_songs .letterbarcontainer' ).hide(); 
				}
				else
				{
					$( '#card_songs .letterbarcontainer' ).show();
				}
				streeme.hideSongAlpha = false;
				return true;
				break;
		}
		
		//fetch data
		$.ajax
		(
			{ 
				url 		: listurl,
				data		: ajaxparameters, 
				success : function( msg )
				{
					result = $.parseJSON( msg );
					var html = '';
					var rows = 0;
					//build and place html
					for ( var i in result )
					{
						html += '<li onclick="streeme.choose( \'' + listname + '\', \'' + result[i]['id'] + '\' )">' + result[i]['name'] + '</li>';
						rows++;
					}
					
					//this spacer pads the content to force the url bar off the screen for small result sets
					var spacer = '';
					if ( rows < 9 )
					{
						spacer = '<div style="height: 330px"></div>';
					}
					
					listcontainer.html( '<ul>' + htmlprefix + html + '</ul>' + spacer );
				}
			}
		);	
	},
	
	/**
	 * Update the menu button state in the upper left corner of the interface 
	 */
	updateSongMenu : function()
	{
		if( streeme.genre_id )
		{
			$('#songbuttonwrapper').html( '<div id="songsmenu" class="headerbutton" onclick="streeme.chooseState( \'card_songs\', \'card_genres\');">&#9668;&nbsp;' + _genres + '</div>' );	
		}
		else if( streeme.playlist_id )
		{
			$('#songbuttonwrapper').html( '<div id="songsmenu" class="headerbutton" onclick="streeme.chooseState( \'card_songs\', \'card_playlists\');">&#9668;&nbsp;' + _playlists + '</div>' );	
		}
		else if( !streeme.artist_id && !streeme.album_id )
		{
			$('#songbuttonwrapper').html( '<div id="songsmenu" class="headerbutton" onclick="streeme.chooseState( \'card_songs\', \'card_welcome\');">&#9668;&nbsp;' + _menu + '</div>' );
		}
		else
		{
			$('#songbuttonwrapper').html( '<div id="songsmenu" class="headerbutton" onclick="streeme.chooseState( \'card_songs\', \'card_albums\');">&#9668;&nbsp;' + _albums + '</div>' );
		}
	},
	
	/** 
	* Update the song selection criteria when the song card is loaded
	* @param listname		str: artist|album|genre|playlist
	*/
	getSongData : function()
	{		
		// create the special keywords for the service
		keyword = '';
		if ( streeme.artist_id != false && streeme.artist_id != null)
		{
			keyword += 'artistid:' + streeme.artist_id + ' ';
		}
		if ( streeme.album_id != false && streeme.album_id != null)
		{
			keyword += 'albumid:' + streeme.album_id + ' ';
		}
		if ( streeme.genre_id != false && streeme.genre_id != null)
		{
			keyword += 'genreid:' + streeme.genre_id + ' ';
		}
		if ( streeme.playlist_id != false && streeme.playlist_id != null)
		{
			keyword += 'playlistid:' + streeme.playlist_id + ' ';
		}
		if ( streeme.shuffle != false && streeme.shuffle != null)
		{
			keyword += 'shuffle:1 ';
		}
		if ( streeme.keywords != false && streeme.keywords != null )
		{
			keyword += streeme.keywords + ' ';
		}
		if ( streeme.song_alpha != false && streeme.song_alpha != null )
		{
			if( !streeme.hideSongAlpha )
			{
				keyword += 'by_alpha:' + streeme.song_alpha + ' ';
			}
		}		
		if  ( $( '#songlist_filter input' ) )
		{
			$( '#songlist_filter > input' ).val( keyword );
			$( '#songlist_filter > input' ).trigger( 'keyup' );
		}
	},
	
	/**
	* Strip HTML from a string
	* @param string to modify
	* @return string cleaned of HTML tags
	*/
	stripTags : function( sString )
	{
		if( sString == null ) return null;
		return sString.replace(/<\/?[^>]+>/gi, '');
	}
}