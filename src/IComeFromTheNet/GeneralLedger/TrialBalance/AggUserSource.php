<?php
namespace IComeFromTheNet\GeneralLedger\TrialBalance;

use DateTime;
use Doctrine\DBAL\Connection;
use IComeFromTheNet\GeneralLedger\Exception\LedgerException;
use Doctrine\DBAL\Types\Type as DoctineType;

/**
 * Will Account balances from the Agg User Database table.
 * 
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @since 1.0
 */ 
class AggUserSource extends AggAllSource 
{
    
    /**
     * @var integer the database if for the ledger user
     */ 
    protected $iUserID;
    
    
    public function __construct(DateTime $oTrialDate, Connection $oDatabase, $aTableMap, $iUserID)
    {
        parent::__construct($oTrialDate, $oDatabase, $aTableMap);
        
        $this->iUserID = $iUserID;
    }
    
    
    public function getUser()
    {
        return $this->iUserID;
    }
    
    
    
    public function getAccountBalances()
    {
        $iUserId    = $this->getUser();        
        $oDatabase  = $this->getDatabaseAdapter();
        $oTrialDate = $this->getTrialDate();
        $oTableMap  = $this->getTableMap();
        $sTableName = $oTableMap['ledger_daily'];
        $sSql       = '';
        
        $sSql .=' SELECT sum(balance) as balance, account_id as account_id';
        $sSql .=" FROM $sTableName ";
        $sSql .=' WHERE process_dt <= :toDate AND user_id = :iUserID ';
        $sSql .=' GROUP BY account_id';
        
        $oSTH = $oDatabase->executeQuery($sSql
                            ,array(':toDate'=> $oTrialDate, ':iUserID'=>$iUserId)
                            ,array(DoctineType::getType('date'),DoctineType::getType('integer'))
                            );
        
        
        while ($aResult = $oSTH->fetch(\PDO::FETCH_ASSOC)) {
            $aResult['account_id'] =  DoctineType::getType('integer')->convertToPHPValue($aResult['account_id']);
            $aResult['balance']    =  DoctineType::getType('float')->convertToPHPValue($aResult['balance']);
        }
        
        
        return $aResults;
        
    }
    
    
    
    
}
/* End of File */
