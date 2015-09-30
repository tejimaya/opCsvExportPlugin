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
 * @author     Kaoru Nishizoe <nishizoe@tejimaya.com>
 */
class opMemberCsvList
{
  private
    $lineTerminate   = "\r\n",
    $recordEndOfLine = "\r\n",
    $fieldsTerminate = ',',
    $escape          = '"',
    $enclose         = '"';

  const UTF8 = 'UTF-8';
  const SJIS = 'SJIS-win';

  static private $encodes = array(
    self::UTF8 => 'UTF-8',
    self::SJIS => 'SJIS',
  );

  // default UTF-8.
  private $usedEncode = self::UTF8;

  private $profileList = array();

  public function __construct()
  {
    $this->profileList = Doctrine_Core::getTable('Profile')->retrievesAll();
  }

  public function getMemberCsvList($from, $to)
  {
    $memberList = $this->getMember($from, $to);
    $memberConfigList = $this->getMemberConfig($from, $to);
    $memberImageList = $this->getMemberImage($from, $to);
    $memberProfileRootList = $this->getMemberProfile($from, $to, true);
    $memberProfileList = $this->getMemberProfile($from, $to, false);
    $profileOptionTranslationList = $this->getProfileOptionTranslation();
    $header = $this->getHeader();

    $csvDatas = $this->makeCsvList($memberList, $memberConfigList, $memberImageList, $memberProfileRootList, $memberProfileList, $profileOptionTranslationList, $header);

    return $csvDatas;
  }

  private function getString($str)
  {
    return is_null($str) || false === $str ? '' : $this->getConvertStrToCsv($str);
  }

  private function getMember($from, $to)
  {
    $parameters = array();

    $select = 'select m.id as m_id';
    $select .= ', m.name as m_name';
    $select .= ', m.created_at as m_created_at';
    $select .= ', m.invite_member_id as m_invite_member_id';
    $select .= ' from member m';
    $select .= ' where m.id >= ?';
    $parameters[] = $from;

    if (!is_null($to))
    {
      $select .= ' and m.id <= ?';
      $parameters[] = $to;
    }
    $select .= ' order by m.id';

    $con = Doctrine_Manager::connection();
    $memberConfigList = $con->fetchAll($select, $parameters);

    $memberDatas = array();
    foreach ($memberConfigList as $memberConfig)
    {
      $id = $memberConfig['m_id'];
      if (!isset($memberDatas[$id]))
      {
        $memberDatas[$id] = array();
      }
      $memberDatas[$id][] = $memberConfig;
    }

    return $memberDatas;
  }

  private function getMemberConfig($from, $to)
  {
    $parameters = array();

    $select = 'select c.member_id as c_member_id';
    $select .= ', c.id as c_id';
    $select .= ', c.name as c_name';
    $select .= ', c.value as c_value';
    $select .= ', c.value_datetime as c_value_datetime';
    $select .= ' from member_config c';
    $select .= ' where c.member_id >= ?';
    $parameters[] = $from;

    if (!is_null($to))
    {
      $select .= ' and c.member_id <= ?';
      $parameters[] = $to;
    }
    $select .= ' order by c.member_id, c.id';

    $con = Doctrine_Manager::connection();
    $memberConfigList = $con->fetchAll($select, $parameters);

    $memberConfigDatas = array();
    foreach ($memberConfigList as $memberConfig)
    {
      $memberId = $memberConfig['c_member_id'];
      if (!isset($memberConfigDatas[$memberId]))
      {
        $memberConfigDatas[$memberId] = array();
      }
      $memberConfigDatas[$memberId][] = $memberConfig;
    }

    return $memberConfigDatas;
  }

  private function getMemberImage($from, $to)
  {
    $parameters = array();

    $select = 'select i.member_id as i_member_id';
    $select .= ', i.file_id as i_file_id';
    $select .= ', i.is_primary as i_is_primary';
    $select .= ', f.name as f_name';
    $select .= ' from member_image i';
    $select .= ' left join file f';
    $select .= ' on i.file_id = f.id';
    $select .= ' where i.member_id >= ?';
    $parameters[] = $from;

    if (!is_null($to))
    {
      $select .= ' and i.member_id <= ?';
      $parameters[] = $to;
    }
    $select .= ' order by i.member_id';

    $con = Doctrine_Manager::connection();
    $memberImageList = $con->fetchAll($select, $parameters);

    $memberImageDatas = array();
    foreach ($memberImageList as $memberImage)
    {
      $id = $memberImage['i_member_id'];
      if (!isset($memberImageDatas[$id]))
      {
        $memberImageDatas[$id] = array();
      }
      $memberImageDatas[$id][] = $memberImage;
    }

    return $memberImageDatas;
  }

  private function getMemberProfile($from, $to, $isRoot)
  {
    $parameters = array();

    $select = 'select p.member_id as p_member_id';
    $select .= ', p.profile_id as p_profile_id';
    $select .= ', p.profile_option_id as p_profile_option_id';
    $select .= ', p.value as p_value';
    $select .= ', p.value_datetime as p_value_datetime';
    $select .= ', p.public_flag as p_public_flag';
    $select .= ' from member_profile p';
    if (true == $isRoot)
    {
      $select .= ' where p.level = 0';
      $select .= ' and p.id = p.tree_key';
    }
    else
    {
      $select .= ' where p.level <> 0';
    }

    $select .= ' and p.member_id >= ?';
    $parameters[] = $from;

    if (!is_null($to))
    {
      $select .= ' and p.member_id <= ?';
      $parameters[] = $to;
    }
    $select .= ' order by p.member_id';

    $con = Doctrine_Manager::connection();
    $memberProfileList = $con->fetchAll($select, $parameters);

    $memberProfileDatas = array();
    foreach ($memberProfileList as $memberProfile)
    {
      $id = $memberProfile['p_member_id'];
      if (!isset($memberProfileDatas[$id]))
      {
        $memberProfileDatas[$id] = array();
      }
      $memberProfileDatas[$id][] = $memberProfile;
    }

    return $memberProfileDatas;
  }

  private function getProfileOptionTranslation()
  {
    $parameters = array();

    $select = 'select o.id as o_id';
    $select .= ', o.value as o_value';
    $select .= ', o.lang as o_lang';
    $select .= ' from profile_option_translation o';
    $select .= ' where o.lang = ?';
    $select .= ' order by o.id';
    $parameters[] = sfContext::getInstance()->getUser()->getCulture();

    $con = Doctrine_Manager::connection();
    $profileOptionTranslationList = $con->fetchAll($select, $parameters);

    $profileOptionTranslationDatas = array();
    foreach ($profileOptionTranslationList as $translation)
    {
      $id = $translation['o_id'];
      $profileOptionTranslationDatas[$id] = $translation['o_value'];
    }

    return $profileOptionTranslationDatas;
  }

  private function getHeader()
  {
    $columnNames = array(
        'id',
        'name',
        'created_at',
        'invite_member_id',
        'lastLogin',
        'pc_address',
        'mobile_address',
    );
    for ($i = 1; $i <= 3; ++$i)
    {
      $columnNames[] = 'memberImage'.$i;
    }

    foreach ($this->profileList as $profile)
    {
      if ($profile->isPreset())
      {
        $presetConfig = $profile->getPresetConfig();
        $columnNames[] = sfContext::getInstance()->getI18n()->__($presetConfig['Caption']);
      }
      else
      {
        $columnNames[] = $profile->getCaption();
      }
    }

    return $this->getCsvLine($columnNames);
  }

  private function makeCsvList($memberList, $memberConfigList, $memberImageList, $memberProfileRootList, $memberProfileList, $profileOptionTranslationList, $header)
  {
    $members = array();
    foreach ($memberList as $memberId => $memberDatas)
    {
      $data = array();
      $data['m_id'] = $memberDatas[0]['m_id'];
      $data['m_name'] = $memberDatas[0]['m_name'];
      $data['m_created_at'] = $memberDatas[0]['m_created_at'];
      $data['m_invite_member_id'] = $memberDatas[0]['m_invite_member_id'];

      $configDatas = $memberConfigList[$memberId];
      $_configData = array();
      foreach ($configDatas as $configData)
      {
        if ('lastLogin' == $configData['c_name'])
        {
          $_configData[$configData['c_name']] = $configData['c_value_datetime'];
        }
        else
        {
          $_configData[$configData['c_name']] = $configData['c_value'];
        }
      }
      $data['config_data'] = $_configData;

      $images = array();
      if (isset($memberImageList[$memberId]))
      {
        $imageDatas = $memberImageList[$memberId];
        $imageIndex = 1;
        foreach ($imageDatas as $imageData)
        {
          $images['image_'.$imageIndex] = $imageData['f_name'];
          $imageIndex++;
        }
      }
      $imageIndex = 1;
      for ($i = 1; $i <= 3; $i++)
      {
        if (isset($images['image_'.$i]))
        {
          $data['image_'.$i] = $images['image_'.$i];
        }
        else
        {
          $data['image_'.$i] = '';
        }
      }

      if (isset($memberProfileRootList[$memberId]))
      {
        $profileRootDatas = $memberProfileRootList[$memberId];
        $profiledatas = array();
        foreach ($profileRootDatas as $profileRootData)
        {
          $profileRootId = $profileRootData['p_profile_id'];
          $profileDatas = null;
          if (isset($memberProfileList[$memberId]))
          {
            $profileDatas = $memberProfileList[$memberId];
            $optionValue = array();
            foreach ($profileDatas as $profileData)
            {
              $childProfileId = $profileData['p_profile_id'];
              if ($childProfileId == $profileRootId)
              {
                $optionId = $profileData['p_profile_option_id'];
                $optionValue[] = $profileOptionTranslationList[$optionId];
              }
            }
            if (count($optionValue) > 0)
            {
              $profiledatas['p_'.$profileRootData['p_profile_id']] = implode(',', $optionValue);
            }
            else
            {
              $profiledatas['p_'.$profileRootData['p_profile_id']] = $profileRootData['p_value'];
            }
          }
          else
          {
            $optionId = $profileRootData['p_profile_option_id'];
            if (is_null($optionId))
            {
              $profiledatas['p_'.$profileRootData['p_profile_id']] = $profileRootData['p_value'];
            }
            else
            {
              $optionValue = array();
              $optionValue[] = $profileOptionTranslationList[$optionId];
              if (count($optionValue) > 0)
              {
                $profiledatas['p_'.$profileRootData['p_profile_id']] = implode(',', $optionValue);
              }
              else
              {
                $profiledatas['p_'.$profileRootData['p_profile_id']] = $profileRootData['p_value'];
              }
            }
          }
          $data['profile_data'] = $profiledatas;
        }
      }
      else
      {
        $data['profile_data'] = null;
      }
      $members[] = $data;
    }

    $memberCsvDatas = array();
    $memberCsvDatas[] = $header;
    foreach ($members as $member)
    {
      $line = array();
      foreach ($member as $key => $value)
      {
        if ('config_data' != $key && 'profile_data' != $key)
        {
          $line[] = $this->getString($value);
        }
        else if ('config_data' == $key)
        {
          $configNames = array(
              'lastLogin',
              'pc_address',
              'mobile_address',
          );
          foreach ($configNames as $configName)
          {
            if (isset($value[$configName]))
            {
              $line[] = $this->getString($value[$configName]);
            }
            else
            {
              $line[] = '';
            }
          }
        }
        else
        {
          foreach ($this->profileList as $profile)
          {
            $profileId = $profile['id'];
            if (isset($value['p_'.$profileId]))
            {
              $line[] = $this->getString($value['p_'.$profileId]);
            }
            else
            {
              $line[] = '';
            }
          }
        }
      }
      $memberCsvDatas[] = $this->getCsvLine($line);
    }

    return $memberCsvDatas;
  }

  private function getConvertStrToCsv($str)
  {
    $value = str_replace(
      array("\r\n", "\r", "\n"), // replace target.
      array("\n", "\n", $this->recordEndOfLine), // replace to.
      $str
    );

    if (preg_match(sprintf('/[,\r\n%s]/', preg_quote($this->enclose, '/')), $value))
    {
      // Escape enclose char.
      $value = str_replace($this->enclose, $this->escape.$this->enclose, $value);

      // Escape comma and end of line.
      return $this->enclose.$value.$this->enclose;
    }

    return $value;
  }

  private function getCsvLine(Array $array)
  {
    $line = implode($this->fieldsTerminate, $array).$this->lineTerminate;
    if (self::UTF8 !== $this->usedEncode)
    {
      $line = mb_convert_encoding($line, $this->usedEncode, 'UTF-8');
    }

    return $line;
  }

  public static function getEncodes()
  {
    return self::$encodes;
  }

  public function setEncode($encode)
  {
    if (isset(self::$encodes[$encode]))
    {
      $this->usedEncode = $encode;
    }
  }
}
