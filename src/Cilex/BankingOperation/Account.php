<?php
/**
 *
 * @author Ghanu <khanraj.2k5@gmail.com>
 */
namespace Cilex\BankingOperation;

use Cilex\BankingOperation\BankingOperation;
abstract class Account implements BankingOperation
{
    
    const TYPE_CURRENT  = 'current';
    const TYPE_SAVINGS  = 'savings';
    const ACCOUNT_INFO_FILE_NAME = 'accounts.json';
    
    private $accountNumber;
    
    private $totalBalance = 0.00;
    protected $overdraftAmount = 0.0;
    
    private $accountOpen  = true;    
    protected $accountsInfo;
    
    public function __construct($accountNumber)
    {
        $this->accountNumber = $accountNumber;
        $this->accountsInfo = json_decode(file_get_contents(self::ACCOUNT_INFO_FILE_NAME), true);
    }

    
    /**
     * getAccountNumber - returns the account number
     * 
     * @return mixed string|int
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }
    
    /**
     * getBalance - returns the balance of the account
     * 
     * @return double
     */
    public function getBalance()
    {
        if (!empty($this->accountsInfo[$this->accountNumber]['total_balane'])) {
            return $this->accountsInfo[$this->accountNumber]['total_balane'];
        }
        if (!empty($this->totalBalance)) {
            return (double) $this->totalBalance;
        }
    }
    
    /**
     * 
     * @return boolean
     */
    public function isAccountExist()
    {
        if (!empty($this->accountsInfo[$this->accountNumber])) {
            return true;
        }
        return false;
    }
    /**
     * open - sets the account open flag to true
     */
    public function open() 
    {
        $this->accountOpen = true;
    }
    
    /**
     * close - sets the account open flag to false
     */
    public function close()
    {
        $this->accountOpen = false;
    }
    
    /**
     * isOpen - returns whether the account is open or not
     * @return boolean
     */
    public function isOpen()
    {
        return (bool) $this->accountOpen; 
    }
    
    /**
     * deposit - deposit an amount into the account
     *
     * @param double $amount - the value to deposit
     * @return boolean
     */
    public function deposit($amount)
    {
        if (is_double($amount)) {
            $this->totalBalance = $this->totalBalance + (double) $amount;
        
            return true;
        }
        
        return false;
    }
    
    /**
     * 
     * @param sting $accountType
     */
    public function saveAccountDetails($accountType)
    {
        $accountsInfo = $this->accountsInfo;
        $accountDetails[$this->accountNumber]['total_balane'] = $this->totalBalance;
        $accountDetails[$this->accountNumber]['account_type'] = $accountType;
        $accountDetails[$this->accountNumber]['overdraft_amount'] = $this->getOverdraftLimit();
        $accountDetails[$this->accountNumber]['is_open'] = 1;
        if (is_array($accountsInfo)) {
            $accountsInfo = $accountsInfo +  $accountDetails;
        } else {
            $accountsInfo = $accountDetails;
        }
        $return = file_put_contents(self::ACCOUNT_INFO_FILE_NAME, json_encode($accountsInfo, true));
    }
    
    /**
     *
     * To update existing record
     */
    public function updateAccountDetails()
    {
        $accountsInfo = $this->accountsInfo;
        $accountsInfo[$this->accountNumber]['total_balane'] = $this->totalBalance;
        $accountsInfo[$this->accountNumber]['is_open'] = 1;
        if (!$this->accountOpen) {
            $accountsInfo[$this->accountNumber]['is_open'] = 0;
        }
        $return = file_put_contents(self::ACCOUNT_INFO_FILE_NAME, json_encode($accountsInfo, true));
    }
    
    /**
     * @param string $accountNumber
     * @param sting $accountType
     */
    public function getAccountDetails($accountNumber, $accountType)
    {
        if (!empty($this->accountsInfo[$accountNumber])) {
            $this->accountNumber = $accountNumber;
            $this->totalBalance = $this->accountsInfo[$accountNumber]['total_balane'];
            $this->overdraftAmount = $this->accountsInfo[$accountNumber]['overdraft_amount'];
            return true;
        }
        return false;
    }
    
    /**
     * updateBalance - update the balance of the account
     *
     * @param double $amount - the value to subtract off balance
     * @return boolean
     */
    protected function updateBalance($amount) {
        $this->totalBalance  = $this->totalBalance - (double) $amount;
    }
    
    /**
     * withdraw - withdraws funds from the account
     *
     * @param double $withdrawAmount amount to withdraw
     * @return boolean
     */
    abstract public function withdraw($withdrawAmount);
    
    /**
     * hasFunds - returns whether the account has funds
     * 
     * @retuen boolean
     */
    abstract public function hasFunds();
}
