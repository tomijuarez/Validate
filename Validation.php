 <?php

 /**
  * Validation Class
  * Validates all data received via http requests [GET | POST [ FILES ] ].
  * It also provides a set of basic utilities to validate any information.
  * @version    1.0
  * @link       http://github.com/tomirammstein
  * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
  * @author     Tomás Sebastián Juárez <tomirammstein@gmail.com>
  *	
  */

Class Validation {
  /**
   * @static {array}  
   * List of the regular expressions for validation utilities.
   */
  protected static $_rules = [
    /**
     * MAL HECHAS :[ 
     **/
      "int" 	        => '/^\d+$/'
    , "float"		=> '/^\d+(\.\d{1,2})?/'
    , "alpha"		=> '/^[a-zA-Z]+$/'
    , "alphanumeric" 	=> '/^[a-zA-Z\d]+$/'
    , "symbols"		=> '[\s\S]'
    , "path"		=>  '#^[a-zA-Z0-9_\\\\:-]+#'
    , "nick"      	=> '/^[0-9a-zA-Z_]{5,20}$/'
    , "password"  	=> '/^.*(?=.{8,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/'
    , "birthday" 	=> '/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/'
    , "email"     	=> '/^[_a-z0-9.-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'
    , "name"      	=> '/^[a-zA-Z -]+$/'
    , "country"   	=> '/^[a-zA-Z]+$/'
  ];
  
  /**
   * This var determine if the user need validation for files or data.
   * @static {array}
   */

  protected static $_situationsList = [
      "image"
    , "video"
    , "doc"
    , "data"
  ];

  /**
   * Set the max size for http-requests.
   * By default, the value is 5MB (in bytes).
   * @static {int}
   * @see setMaxSize()
   */

  private static $_maxSize = 5242880;

  /**
   * Determine if the Class must validate the extention of the files received.
   * This var is only procceced if any file is received.
   * @static {bool}
   * @see setAllowExtension()
   */

  private static $_allowExt = false;
  
  /**
   * @static {array} 
   * A list with the allowed file extensions.
   */

  private static $_extName = [
       "image" => [
             "jpg"
           , "jpeg"
	   , "bmp"
	   , "gif"
           , "png"
       ]
     , "video" => [
             "wmv"
	   , "mp4"
	   , "3gp"
       ]
     , "music" => [
	     "mp3"
           , "ogg"
       ]
    , "doc"    => [
             "doc"
	   , "docx"
	   , "ppt"
	   , "pdf"
	   , "txt"
       ]
  ];

  /**
   * The file type that the user must to validate [FILE | DATA]
   */
  
  private $_situation;

  /**
   * Expected keys in the data.
   * @access private
   * @var _requiredFlags {array}
   */
  
  private $_requiredFlags;
  
  /**
   * Errors that the user get if something goes wrong.
   * @access private
   * @var _errors {array}
   * @see getMessages()
   */
  
  private $_errors = [ ];

  public function __construct () {
    //[BLANK]			
  }

  /**
   * @param flags {array} - set the keys and the expected type of values that the $inputs param must to be.
   */
  
  public function setFlags ( Array $flags ) {
    $this->_requiredFlags = $flags;
  }

  /**
   * @param allowExt {bool} - Determines the extensions that the Class must validates if the data received containes any file.
   */

  public function setAllowExtentions ( $allowExt ) {
    $this->_allowExt = (bool) $allowExt;
  }

  /**
   * @param {int} - This value is multiplied by 1024.
   */

  public function setMaxSize ( $maxSize ) {
    $this->_maxSize = ( (int)$maxSize * 1024 ) * 1024;
  }

  /**
   * @return {array}
   */
  public function getMessages () {
    return $this->_errors;
  }

  /**
   * @return {bool}
   * @throws Exception
   */
  
  public function validate ( $inputs, $situationKey = "data" ) {

    if ( in_array ( $situationKey, array_values ( self::$_situationsList ) ) ) {
      $passed = true;
      foreach ( $this->_requiredFlags as $requiredKey => $requiredValue ) {
        if ( !in_array ( $requiredKey, array_keys ( $inputs ) ) ) {
	  $passed = false;
	  $this->_errors [ 'matches' ] [ $requiredKey ] = $requiredKey.' must be defined.';
	}
	else {
	  if ( !empty ( $inputs [ $requiredKey ] ) &&  !trim ( $inputs [ $requiredKey ] ) == '' ) {
	    if ( !preg_match ( self::$_rules [ $requiredValue ], $inputs [ $requiredKey ] ) ) {
	      $passed = false;
	      $this->_errors [ 'regex' ] [ $requiredKey ] = $requiredKey. ' does not match with the '.$requiredValue. ' property';
	    }
	    else {
	      if ( $situationKey != 'data' ) {
	        $passed = $this->_validateFile($situationKey, $inputs);
	      }
	    }
	  }
	  else {
	    $passed = false;
	    $this->_errors [ 'blank' ] [ $requiredKey ] = $requiredKey. ' can\'t be empty ';
	  }
	}
      }
      return $passed;
    }
    throw new Exception ( 'Invalid key' );
  }

  /**
   * If some file must to be validated, the validate function call this function to proccess the file uploaded. 
   * @return {bool}
   */

  private function _validateFile ( $key, $file ) {
    $passed         = true;
    $fileName       = (string) $file [ 'tmp_name' ];
    $fileSize       = (int)    $file [ 'size' ];
    $fileClientName = (string) $file [ 'name' ];
    $fileType       = (string) pathinfo ( $fileClientName, PATHINFO_EXTENSION );

    if ( !is_file( $fileName ) ) {
      $passed = false;
      $this->_errors ['file'] = 'There\'s no file assigned.';
    }
    else {
      if ( !file_exists( $fileName ) ) {
        $passed = false;
	$this->_errors ['file'] = 'That file doesn\'t exists, it seems.';
      }
      else {
	if ( $fileSize > self::$_maxSize ) {
	  $passed = false;
	  $this->_errors ['file'] ['size'] = 'The uploaded file size must be less than 5MB.';
	}
        else {
	  if ( !self::$_allowExt ) {
	    if ( !in_array ( $fileType, self::$_extName [ $key ] ) ) {
	      $passed = false;
	      $this->_errors ['file'] ['extension'] = 'The uploaded file extension is not allowed.';
	    }
	  }
        }
      }
    }
    return $passed;
  }
  
}
