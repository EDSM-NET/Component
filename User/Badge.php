<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\User;

use         Alias\Commander\Badge   AS BadgeAlias;

trait Badge
{
    protected $_badges                  = false;
    protected $_haveReceivedBadgeEmail  = false;

    private function getBadges()
    {
        if($this->_badges === false)
        {
            $this->_badges = self::getModel('Models_Users_Badges')->getByRefUser($this->getId());
        }

        return $this->_badges;
    }

    public function haveBadge($badgeId)
    {
        if($this->isValid() && $this->getRole() != 'guest' && BadgeAlias::isIndex($badgeId))
        {
            $badges = $this->getBadges();

            if(!is_null($badges) && count($badges) > 0)
            {
                foreach($badges AS $badge)
                {
                    if($badge['refBadge'] == $badgeId)
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function getBadgeDetail($badgeId)
    {
        if($this->isValid() && $this->getRole() != 'guest' && BadgeAlias::isIndex($badgeId))
        {
            $badges = $this->getBadges();

            if(!is_null($badges) && count($badges) > 0)
            {
                foreach($badges AS $badge)
                {
                    if($badge['refBadge'] == $badgeId)
                    {
                        return $badge;
                    }
                }
            }
        }

        return null;
    }

    public function giveBadge($badgeId, $extraDetail = null)
    {
        if($this->isValid() && $this->getRole() != 'guest')
        {
            if(BadgeAlias::isIndex($badgeId) && BadgeAlias::isActive($badgeId))
            {
                if($this->haveBadge($badgeId) === false)
                {
                    try
                    {
                        $insert = array(
                            'refUser'       => $this->getId(),
                            'refBadge'      => $badgeId,
                            'dateObtained'  => new \Zend_Db_Expr('NOW()'),
                        );

                        if(!is_null($extraDetail))
                        {
                            if($badgeId == 7800)
                            {
                                $insert['extraDetail'] = \Zend_Json::encode(array('records' => array($extraDetail)));
                            }
                            else
                            {
                                $insert['extraDetail'] = \Zend_Json::encode($extraDetail);
                            }
                        }

                        self::getModel('Models_Users_Badges')->insert($insert);

                        // No badge given while clearing or deleting saves!
                        if(defined('Process_User_Delete_noGivingBadge'))
                        {
                            return;
                        }

                        $badge = BadgeAlias::get($badgeId);

                        if($this->_haveReceivedBadgeEmail === false && $this->receiveEmailBadges() === true && APPLICATION_ENV == 'production' && $badgeId != 65535)
                        {
                            $mail = new \EDSM_Mail();
                            $oldLanguage = $mail->getView()->language; // Get old language

                            $mail->setTemplate('badge.phtml');
                            $mail->setLanguage($this->getLocale());
                            $mail->setVariables(array(
                                'commanderId'       => $this->getId(),
                                'commander'         => $this->getCMDR(),
                                'badgeId'           => $badgeId,
                                'badgeName'         => $badge['name'],
                                'badgeDescription'  => $badge['description'],
                                'badgeImage'        => BadgeAlias::getImageSrc($badgeId),
                                'extraDetail'       => $extraDetail,
                            ));

                            $mail->addTo($this->getEmail());

                            $mail->setSubject(
                                $mail->getView()->translate('EMAIL\Badge unlocked! - %1$s' , $badge['name'])
                            );
                            $mail->send();
                            $mail->closeConnection();

                            $mail->setLanguage($oldLanguage); // Reset language

                            $this->_haveReceivedBadgeEmail = true;
                        }
                    }
                    catch(\Zend_Exception $e)
                    {
                        // Do nothing, too bad the user will not see our badge email :)
                    }
                }
                else
                {
                    // Try to update extraDetail if needed
                    if(!is_null($extraDetail))
                    {
                        $currentBadges  = self::getModel('Models_Users_Badges')->getByRefUser($this->getId());
                        $currentBadge   = null;

                        foreach($currentBadges AS $testBadge)
                        {
                            if($testBadge['refBadge'] == $badgeId)
                            {
                                $currentBadge = $testBadge;
                                break;
                            }
                        }

                        if(!is_null($currentBadge))
                        {
                            // Records are merged
                            if($badgeId == 7800)
                            {
                                if(!array_key_exists('extraDetail', $currentBadge) || is_null($currentBadge['extraDetail']))
                                {
                                    $currentBadgeExtraDetail = array('records' => array());
                                }
                                else
                                {
                                    $currentBadgeExtraDetail = \Zend_Json::decode($currentBadge['extraDetail']);
                                }

                                // Convert to new format
                                if(!array_key_exists('records', $currentBadgeExtraDetail))
                                {
                                    $currentBadgeExtraDetail = array('records' => array($currentBadgeExtraDetail));
                                }

                                // Check if already present in the details
                                $addNewRecord = true;

                                foreach($currentBadgeExtraDetail['records'] AS $key => $record)
                                {
                                    if(serialize($record) == serialize($extraDetail))
                                    {
                                        $addNewRecord = false;
                                        break;
                                    }
                                }

                                if($addNewRecord === true)
                                {
                                    $currentBadgeExtraDetail['records'][] = $extraDetail;
                                }

                                $currentBadgeExtraDetail = \Zend_Json::encode($currentBadgeExtraDetail);
                                if(!array_key_exists('extraDetail', $currentBadge) || $currentBadgeExtraDetail != $currentBadge['extraDetail'])
                                {
                                    self::getModel('Models_Users_Badges')->updateById($currentBadge['id'], array('extraDetail' => $currentBadgeExtraDetail));
                                }
                            }
                            else
                            {
                                $extraDetail = \Zend_Json::encode($extraDetail);

                                if(!array_key_exists('extraDetail', $currentBadge) || $extraDetail != $currentBadge['extraDetail'])
                                {
                                    self::getModel('Models_Users_Badges')->updateById($currentBadge['id'], array('extraDetail' => $extraDetail));
                                }
                            }
                        }
                    }
                }
            }

            // Dependent badges ^^
            if($this->haveBadge(10) && $this->haveBadge(20) && $this->haveBadge(30) && $this->haveBadge(50) === false)
            {
                $this->giveBadge(50);
            }
        }
    }
}