<?php
/**
 * This is the form used by the setup page to expose app.yml setup
 *
 * @author Richard Hoar
 * @package streeme
 */
class ApplicationSetupForm extends BaseForm
{
  private function getLibraryChoices()
  {
    return array(
      '1'=>sfContext::getInstance()->getI18N()->__('Import from iTunes'),
      '2'=>sfContext::getInstance()->getI18N()->__('Use Media Folders')
    );
  }

  private function getSongsPerPageChoices()
  {
     return array(
      '10' => '10',
      '20' => '20',
      '30' => '30',
      '40' => '40',
      '50' => '50',
      '60' => '60',
      '70' => '70',
      '80' => '80',
      '90' => '90',
      '100' => '100',
      '110' => '110',
      '120' => '120',
    );
  }
  
  private function  getTranscodingChoices()
  {
    return array(
      'no'  => sfContext::getInstance()->getI18N()->__('No'),
      'yes' => sfContext::getInstance()->getI18N()->__('Yes'),
    );
  }
  
  private function  getCookiesChoices()
  {
    return array(
      'no'  => sfContext::getInstance()->getI18N()->__('No'),
      'yes' => sfContext::getInstance()->getI18N()->__('Yes'),
    );
  }
  
  public function configure()
  {
    $this->setWidgets(array(
      'songs_per_page'  => new sfWidgetFormSelect(array('choices'=>$this->getSongsPerPageChoices())),
      'library_type'    => new sfWidgetFormSelect(array('choices'=>$this->getLibraryChoices())),
      'library_paths'   => new sfWidgetFormTextarea(),
      'itunes_path'     => new sfWidgetFormInput(),
      'transcoding'     => new sfWidgetFormSelect(array('choices'=>$this->getTranscodingChoices())),
      'ffmpeg_path'     => new sfWidgetFormInput(),
      'database_indexing' => new sfWidgetFormInputCheckbox(),
      'send_cookies'    => new sfWidgetFormSelect(array('choices'=>$this->getCookiesChoices())),
      'allowed_ips'     => new sfWidgetFormTextarea(),
    ));
    
    $this->setValidators(array(
      'songs_per_page'  => new sfValidatorChoice(array('choices'=>array_keys($this->getSongsPerPageChoices()))),
      'library_type'    => new sfValidatorChoice(array('choices'=>array_keys($this->getLibraryChoices()))),
      'library_paths'   => new sfValidatorPass(),
      'itunes_path'     => new sfValidatorPass(),
      'transcoding'     => new sfValidatorChoice(array('choices'=>array_keys($this->getTranscodingChoices()))),
      'ffmpeg_path'     => new sfValidatorPass(),
      'database_indexing' => new sfValidatorBoolean(),
      'send_cookies'    => new sfValidatorChoice(array('choices'=>array_keys($this->getCookiesChoices()))),
      'allowed_ips'     => new sfValidatorPass(),
    ));
    
    $library_paths = sfConfig::get('app_wf_watched_folders', array(sprintf('%spath%sto%smusic', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR)));
    $allowed_ips   = sfConfig::get('app_setup_acl_allowed_ips', array());
    $this->setDefaults(array(
      'library_type'  => sfConfig::get('setup_library_type', '2'),
      'songs_per_page'  => (int) sfConfig::get('app_results_per_page', 60),
      'itunes_path'     => sfConfig::get('app_itunes_xml_location', sprintf('%spath%sto%siTunes Music Library.xml', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR)),
      'library_paths'   => join("\r\n", array_values($library_paths)),
      'transcoding'     => (sfConfig::get('app_allow_ffmpeg_transcoding', 'false')==true) ? 'yes' : 'no',
      'ffmpeg_path'     => sfConfig::get('app_ffmpeg_executable', sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR .'/../ffmpeg/ffmpeg.exe'),
      'send_cookies'    => (sfConfig::get('send_cookies_with_request', 'false')==true) ? 'yes' : 'no',
      'allowed_ips'     => join("\r\n", array_values($allowed_ips)),
    ));
    $this->widgetSchema->setNameFormat('setup[%s]');
    $this->widgetSchema->setFormFormatterName('list');
  }
}