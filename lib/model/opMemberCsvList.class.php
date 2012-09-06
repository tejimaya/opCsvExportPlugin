<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */


/**
 * opMemberCsvList
 *
 * @package    opCsvExportPlugin
 * @subpackage model
 * @author     Yuya Watanabe <watanabe@tejimaya.com>
 */
class opMemberCsvList implements Iterator
{
  private $memberIds;
  private $position = 0;

  public function __construct($from, $to)
  {
    $query = Doctrine::getTable('Member')->createQuery()->select('id')->where('? <= id', $from);
    if (!is_null($to))
    {
      $query = $query->andWhere('id <= ?', $to);
    }

    $this->memberIds = $query->execute(array(), Doctrine::HYDRATE_NONE);
  }

  public function rewind()
  {
    $this->position = 0;
  }

  public function current()
  {
    return $this->getMemberCsv($this->memberIds[$this->position]);
  }

  public function key()
  {
    return $this->memberIds[$this->position];
  }

  public function next()
  {
    ++$this->position;
  }

  public function valid()
  {
    return 0 <= $this->position && $this->position < count($this->memberIds);
  }

  public function getMemberIds()
  {
    return $this->memberIds;
  }

  public function getMemberCsv($memberId)
  {
    $line = array();

    $member = Doctrine::getTable('Member')->find($memberId);
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
    foreach (Doctrine::getTable('Profile')->retrievesAll() as $profile)
    {
      $line[] = $this->getString($member->getProfile($profile->getName()));
    }

    return '"'.implode('","', $line).'"';
  }

  private function getString($str)
  {
    return is_null($str) || false === $str ? '' : $str;
  }

  static public function getHeader()
  {
    $result = array('id', 'name', 'created_at', 'invite_member_id', 'lastLogin', 'pc_address', 'mobile_address');

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
