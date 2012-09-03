<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opCsvExportForm
 *
 * @package    opCsvExportPlugin
 * @subpackage form
 * @author     Yuya Watanabe <watanabe@tejimaya.com>
 */
class opCsvExportForm extends sfForm
{
  public function configure()
  {
    $this->setWidget('from', new sfWidgetFormInput());
    $this->setValidator('from', new sfValidatorNumber());

    $this->setWidget('to', new sfWidgetFormInput());
    $this->setValidator('to', new sfValidatorNumber());

    $this->getWidgetSchema()->setNameFormat('opCsvExport[%s]');
  }
}
