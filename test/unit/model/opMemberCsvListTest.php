<?php

$app = 'pc_backend';
include(dirname(__FILE__).'/../../bootstrap/unit.php');
include(dirname(__FILE__).'/../../bootstrap/database.php');
if (!sfContext::hasInstance('pc_backend') && $configuration)
{
  sfContext::createInstance($configuration, $app);
}

$t = new lime_test(3);

$t->diag('opMemberCsvList::getMemberCsvList()');

$conn = Doctrine::getTable('Member')->getConnection();
$conn->beginTransaction();

$csvList = new opMemberCsvList();
$csv = $csvList->getMemberCsvList(1, 10000);

$t->is(count($csv), 5, 'count header 1 + member 4');
$datetimeRegex= '[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-2][0-9]:[0-5][0-9]:[0-5][0-9]';
$csvLineRegex1 = <<<EOT
/^
  1,                                       ## ID
  Ohira1,                                  ## name
  $datetimeRegex,                          ## created_at
  ,                                        ## invite_member_id
  $datetimeRegex,                          ## lastLogin
  """o-hira1""@example.com",               ## pc_address
  o-hira-mobile1@example.com,              ## mobile_address
  dummy_file3,                             ## memberImage1
  dummy_file2,                             ## memberImage2
  dummy_file1,                             ## memberImage3
  Man,                                     ## 性別
  1988-04-23,                              ## 誕生日
  Tokyo,                                   ## 都道府県
  """よ,\sろ,\sし,\sく""\sお願いします!!!" ## 自己紹介
\\r\\n$/x
EOT;
$t->like($csv[1], $csvLineRegex1, 'check escaped comma and double quote.');

$csvLineRegex2 = <<<EOT
/^
  2,                                        ## ID
  Ohira2,                                   ## name
  $datetimeRegex,                           ## created_at
  ,                                         ## invite_member_id
  $datetimeRegex,                           ## lastLogin
  """o-hira2""@example.com",                ## pc_address
  o-hira-mobile2@example.com,               ## mobile_address
  ,                                         ## memberImage1
  ,                                         ## memberImage2
  ,                                         ## memberImage3
  Man,                                      ## 性別
  2000-01-01,                               ## 誕生日
  Shimane,                                  ## 都道府県
  "よ\\r\\n
   ろ\\r\\n
   し\\r\\n
   く\\r\\n
   お願いします!!!\\r\\n"                   ## 自己紹介
\\r\\n$/x
EOT;
$t->like($csv[2], $csvLineRegex2, 'check escaped line terminate.');

$conn->rollback();
