<?php
namespace IComeFromTheNet\Ledger\Entity;

use Aura\Marshal\Entity\GenericEntity;
use IComeFromTheNet\Ledger\Exception\LedgerException;
use IComeFromTheNet\Ledger\Voucher\ValidationRuleBag;

/**
  *  Represent a custom Ledger Entry type.
  *
  *  Ledger transaction are often divied into groups, for example
  *
  *  1. General Journals
  *  2. Sales Recepits
  *  3. Invoices
  *  4. etc
  *
  *  As we can't know every group (voucher type) this entity allows
  *  developers to define their own and relate them back to a ledger transaction.
  *
  *  Each voucher is identified by a 'voucher reference'
  *
  *  {prefix}sequence{suffix}
  *
  *  e.g GL_503
  *
  *  Types may share common name and slug but will have a different
  *  enabled date pair.
  *
  *  If change the prefix for sales recepits, that is a new type with
  *  same name.
  *
  *  Each entity has a unique id generated by the database BUT NOT used to
  *  relate in our domain model.
  *
  *  A voucher slug name will is used to establish relationships in the domain.
  *  The ledger will only load the valid entities as of the given date, and
  *  for each name there can be only one valid entity at a given date.
  *
  *  @author Lewis Dyer <getintouch@icomefromthenet.com>
  *  @since 1.0.0
  */
class VoucherType extends GenericEntity
{
    const DESCRIPTION_MAX_SIZE  = 500;
    const NAME_MAX_SIZE         = 100;
    
    
    const FIELD_VOUCHER_TYPE_ID  = 'voucher_type_id';
    const FIELD_NAME             = 'voucher_name';
    const FIELD_DESCRIPTION      = 'voucher_description';
    const FIELD_ENABLE_FROM      = 'voucher_enable_from';
    const FIELD_ENABLE_TO        = 'voucher_enable_to';
    const FIELD_PREFIX           = 'voucher_prefix';
    const FIELD_SUFFIX           = 'voucher_suffix';
    const FIELD_SLUG             = 'voucher_name_slug';
    const FIELD_SEQUENCE_STRATEGY = 'sequence_strategy';
    
    public function __construct()
    {
        $this->__set(self::FIELD_VOUCHER_TYPE_ID,null);
        $this->__set(self::FIELD_NAME,null);
        $this->__set(self::FIELD_DESCRIPTION,null);
        $this->__set(self::FIELD_ENABLE_FROM,null);
        $this->__set(self::FIELD_ENABLE_TO,null);
        $this->__set(self::FIELD_PREFIX,null);
        $this->__set(self::FIELD_SUFFIX,null);
        $this->__set(self::FIELD_SEQUENCE_STRATEGY,null);
        
    }
    
    
    /**
     *  Get the voucher types database ID
     *
     *  @access public
     *  @return integer the database id
     *
    */
    public function getVoucherTypeID()
    {
        return $this->__get(self::FIELD_VOUCHER_TYPE_ID);
    }
    
    /**
     *  Set this vouchers type voucher id
     *
     *  @access public
     *  @return integer $id 
     *
    */
    public function setVoucherTypeID($id)
    {
        if(!is_init($id) || (int) $id <= 0) {
            throw new LedgerException('Voucher type ID must be an integer > 0');
        }
        
        $this->__set(self::FIELD_VOUCHER_TYPE_ID,null);
        
        return $this;
    }
    
    /**
     *  Return this voucher types name
     *
     *  @access public
     *  @return string
     *
    */
    public function getName()
    {
        return $this->__get(self::FIELD_NAME);
    }
    
    /**
     *  Set a name for this voucher type, must not be empty
     *
     *  @access public
     *  @return $this
     *  @param string $name max 100 characters
     *
    */
    public function setName($name)
    {
        if(empty($name)) {
            throw new LedgerException('Voucher type name must not be empty');
        }
        
        if(mb_strlen($name) > self::NAME_MAX_SIZE) {
            throw new LedgerException(printf('Voucher type name must be less than %s characters',self::NAME_MAX_SIZE));
        }
        
        $this->__set(self::FIELD_NAME,$name);
        
        return $this;
    }
    
    /**
     *  Sets the description of this voucher type
     *
     *  @access public
     *  @return void
     *
    */
    public function getDescription()
    {
        return $this->__get(self::FIELD_DESCRIPTION);   
    }
    
    /**
     *  Sets a description for this voucher type
     *
     *  @access public
     *  @return $this
     *  @param string $description max 500 characters
     *
    */
    public function setDescription($description)
    {
        if(mb_strlen($description) > self::DESCRIPTION_MAX_SIZE) {
            throw new LedgerException(printf('Voucher type description must be less than %s characters',self::DESCRIPTION_MAX_SIZE));
        }
     
        $this->__set(self::FIELD_DESCRIPTION,$description);
        
        return $this;
    }
    
    
    /**
     *  Get the date this voucher Type will be available from
     *
     *  @access public
     *  @return DateTime
     *
    */
    public function getEnabledFrom()
    {
        return $this->__get(self::FIELD_ENABLE_FROM);
    }
    
    /**
     *  Set date this voucher Type will be available from
     *
     *  @access public
     *  @return $this
     *  @param DateTime $from
     *
    */
    public function setEnabledFrom(DateTime $from)
    {
         $closed = $this->__get(self::FIELD_ENABLE_TO);
        
        if($closed instanceof DateTime) {
            if($closed <= $opened) {
                throw new LedgerException('Date the voucher type becomes available must occur before it has become unavailable');
            }
        }
        
        $this->__set(self::FIELD_ENABLE_FROM,$from);
        
        return $this;
    }
    
    
    /**
     *  Gets the date this voucher type will be unavailable.
     *
     *  @access public
     *  @return DateTime
     *
    */
    public function getEnabledTo()
    {
        return $this->__get(self::FIELD_ENABLE_TO);
    }
    
    /**
     *  Sets the date this voucher type will be unavailable.
     *
     *  i.e. soft delete.
     *
     *  @access public
     *  @return $this
     *  @param DateTime $to 
     *
    */    
    public function setEnabledTo(DateTime $to)
    {
        $opened = $this->__get(self::FIELD_ENABLE_FROM);
        
        if($opened instanceof DateTime) {
            if($opened >= $to) {
                throw new LedgerException('Date the voucher type becomes unavailable must occur after it has become available');
            }
        }
        
        
        $this->__set(self::FIELD_ENABLE_TO,$to);
        
        return $this;
    }
    
    
    /**
     *  Gets the prefix that attached start of a voucher reference
     *
     *  @access public
     *  @return void
     *
    */
    public function getPrefix()
    {
        return $this->__get(self::FIELD_PREFIX);
    }
    
    /**
     *  Sets a prefix that attached to start of a voucher reference
     *
     *  @access public
     *  @return $this;
     *  @param string $prefix
     *
    */
    public function setPrefix($prefix)
    {
        $this->__set(self::FIELD_PREFIX,$prefix);
        return $this;
    }
    
    /**
     *  Get the suffix that attached to voucher reference
     *
     *  @access public
     *  @return string the suffix
     *
    */
    public function  getSuffix()
    {
        return $this->__get(self::FIELD_SUFFIX);
    }
    
    /**
     *  Sets a suffix to attach to end of a voucher reference
     *
     *  @access public
     *  @return $this;
     *  @param string $suffix
     *
    */
    public function setSuffix($suffix)
    {
        $this->__set(self::FIELD_SUFFIX,$suffix);
        return $this;
    }
    
    /**
     *  Gets the slug rule used to match voucher to validation rule
     *
     *  @access public
     *  @return string the slug
     *
    */
    public function getSlugRule()
    {
        return $this->__get(self::FIELD_SLUG);
    }
    
    /**
     *  Set a slug rule that used to match voucher to validation rule
     *
     *  @access public
     *  @return $this
     *  @param string $slug
     *
    */
    public function setSlugRule($slug)
    {
        $this->__set(self::FIELD_SLUG,$slug);
        return $this;
    }
    
    //-------------------------------------------------------
    
    /**
     *  Gets the sequence strategy.
     *
     *  @access public
     *  @return void
     *
    */
    public function getSequenceStrategy()
    {
        return $this->__get(self::FIELD_SEQUENCE_STRATEGY);
    }
    
    /**
     *  docs
     *
     *  @access public
     *  @return void
     *
    */
    public function setSequenceStrategy()
    {
        
        
        return $this;
    }
    
    
    //-------------------------------------------------------
    
    /**
     *  Generate a reference and test it is valid
     *
     *  @access public
     *  @return void
     *
    */
    public function generateReference(ValidationRuleBag $bag)
    {
        $reference = $this->getPrefix() . $this->getSequenceStrategy()->nextVal() . $this->getSuffix();
        
        if(!$this->validateReference($bag,$reference)) {
            throw new LedgerException('Generated reference failed to validate, maybe sequence is broken');
        }
        
        return $reference;
    }
    
    /**
     *  Validate a reference with matching rule
     *
     *  @access public
     *  @return true if valid
     *
    */
    public function validateReference(ValidationRuleBag $bag,$reference)
    {
        
    }
    
}
/* End of Class */
