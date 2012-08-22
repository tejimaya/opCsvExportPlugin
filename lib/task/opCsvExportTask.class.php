<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */


/**
 * opCsvExportTask
 *
 * @package    opCsvExport
 * @author     Yuya Watanabe <watanabe@tejimaya.com>
 */
class opCsvExportTask extends sfDoctrineBaseTask
{
  protected function configure()
  {
    parent::configure();
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),

      new sfCommandOption('from', null, sfCommandOption::PARAMETER_REQUIRED, 'member id which start from (inclusive)', 1),
      new sfCommandOption('to', null, sfCommandOption::PARAMETER_REQUIRED, 'member id which end to (exclusive)', null),

      new sfCommandOption('header', null, sfCommandOption::PARAMETER_OPTIONAL, 'need to output csv param header', true),
    ));

    $this->namespace        = 'opCsvExport';
    $this->name             = 'export';
  }

  protected function execute($arguments = array(), $options = array())
  {
    new sfDatabaseManager($this->configuration);

    if ('true' === $options['header'])
    {
      echo $this->getHeader()."\n";
    }

    $from = $options['from'];
    $to = $options['to'];

    $table = Doctrine::getTable('Member');

    $query = $table->createQuery()->select('id')->where('? <= id', $from);
    if (!is_null($to))
    {
      $query = $query->andWhere('id < ?', $to);
    }
    $memberIds = $query->execute(array(), Doctrine::HYDRATE_NONE);

    foreach($memberIds as $memberId)
    {
      $member = $table->find($memberId[0]);
      $line = array();
      $line[] = $this->getString($member->getId());
      $line[] = $this->getString($member->getName());
      $line[] = $this->getString($member->getCreatedAt());
      $line[] = $this->getString($member->getInviteMemberId());
      $line[] = $this->getString($member->getConfig('lastLogin'));
      $line[] = $this->getString($member->getConfig('pc_address'));
      $line[] = $this->getString($member->getConfig('mobile_address'));
      $memberImages = $member->getMemberImage();
      for ($i = 0; $i < 3; ++$i)
      {
        $line[] = $this->getString($memberImages[$i]->getFile());
      }
      foreach ($member->getProfiles() as $profile)
      {
        $line[] = $this->getString($profile->getValue());
      }
      echo '"'.implode('","', $line)."\"\n";
    }
  }

  private function getString($str)
  {
    return is_null($str) ? '' : $str;
  }

  private function getHeader()
  {
    $result = array('id','name','created_at','invite_member_id','lastLogin','pc_address','mobile_address');

    for ($i = 1; $i <= 3; ++$i)
    {
      $result[] = 'memberImage'.$i;
    }

    foreach (Doctrine::getTable('Profile')->retrievesAll() as $profile)
    {
      $result[] = $profile->getName();
    }

    return '"'.implode('","', $result).'"';
  }
}
