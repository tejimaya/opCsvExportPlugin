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
 */
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

      $memberCsvList = new opMemberCsvList($this->form->getValue('from'), $this->form->getValue('to'));

      $csvStr = opMemberCsvList::getHeader()."\n";
      foreach ($memberCsvList as $memberCsv)
      {
        $csvStr .= $memberCsv."\n";
      }

      opToolkit::fileDownload('member.csv', $csvStr);

      return sfView::NONE;
    }
  }
}
