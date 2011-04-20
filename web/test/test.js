/* this is a test javascript for combineFilesTest unit test */
//Test if JQuery is Loaded 
if( typeof $ == 'undefined' )
{
		throw ( new Error (
			"\n\n" +
			"This Application Requires the JQuery javascript framework" +
			"\n\n"
		)); 
}

//Create the Streeme Class
if( typeof streeme == 'undefined' ){ streeme = {} }

streeme = {
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
}