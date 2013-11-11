<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * csvExportActions
 *
 * @package    opCsvExportPlugin
 * @author     Yuya Watanabe <watanabe@tejimaya.com>
 * @author     Kaoru Nishizoe <nishizoe@tejimaya.com>
 *  */
class csvExportActions extends sfActions
{
  public function executeDownload(sfWebRequest $request)
  {
    $this->form = new opCsvExportForm();

    if ($request->isMethod(sfRequest::POST))
    {
      $this->form->bind($request->getParameter('opCsvExport'));

      if (!$this->form->isValid())
      {
        return sfView::SUCCESS;
      }

      $csvList = new opMemberCsvList();
      $csvList->setEncode($this->form->getValue('encode'));
      $memberCsvList = $csvList->getMemberCsvList($this->form->getValue('from'), $this->form->getValue('to'));

      $csvStr = '';
      foreach ($memberCsvList as $memberCsv)
      {
        $csvStr .= $memberCsv;
      }

      opToolkit::fileDownload('member.csv', $csvStr);

      return sfView::NONE;
    }
  }
}
