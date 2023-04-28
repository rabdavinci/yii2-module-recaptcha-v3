<?php

namespace rabdavinci\recaptcha3;

use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\Html;
use yii\web\View;
use yii\base\Widget;

/**
 * Class ReCaptchaComponent
 * @package rabdavinci\recaptcha3
 */
class ReCaptchaComponent extends Widget
{
  /**
   * Recaptcha component
   * @var string|array|ReCaptcha
   */
  public $component = 'reCaptcha3';

  /**
   * @var string
   */
  public $buttonText = 'Submit';

  /**
   * @var string
   */
  public $actionName = 'homepage';

  /**
   * @var RecaptchaV3
   */
  private $_component = null;

  /**
   * @inheritdoc
   * @throws InvalidConfigException
   */
  public function init()
  {
    parent::init();
    $component = Instance::ensure($this->component, ReCaptcha::class);
    if ($component == null) {
      throw new InvalidConfigException('component is required.');
    }
    $this->_component = $component;
  }

  /**
   * @inheritdoc
   */
  public function run()
  {
    $this->_component->registerScript($this->getView());
  }
}
