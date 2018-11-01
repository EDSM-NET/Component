<?php
/**
 * Elite Dangerous Star Map
 * @link https://www.edsm.net/
 */

namespace   Component\System;

trait Death
{
    protected $_deathReport         = false;
    protected $_deathReportLast24h  = false;
    protected $_deathReportLast7d   = false;

    public function getDeathsReport()
    {
        if($this->_deathReport === false)
        {
            $this->_deathReport = self::getModel('Models_Users_Deaths')->getDeathsReport($this->getId());
        }

        return $this->_deathReport;
    }

    public function getDeathsReportLast24h()
    {
        if($this->_deathReportLast24h === false)
        {
            $this->_deathReportLast24h = self::getModel('Models_Users_Deaths')->getDeathsReportLast24h($this->getId());
        }

        return $this->_deathReportLast24h;
    }

    public function getDeathsReportLast7d()
    {
        if($this->_deathReportLast7d === false)
        {
            $this->_deathReportLast7d = self::getModel('Models_Users_Deaths')->getDeathsReportLast7d($this->getId());
        }

        return $this->_deathReportLast7d;
    }
}