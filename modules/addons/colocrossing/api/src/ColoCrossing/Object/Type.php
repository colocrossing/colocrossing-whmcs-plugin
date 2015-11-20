<?php

/**
 * Represents an instance of a Type associated to a Device from
 * the API. Holds only the data for the Device's Type. It provides methods
 * to determine the capabilities of the associated Device. That is, it allows
 * you to determine the Rack, Power, and Network configuration of the device type.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_Type extends ColoCrossing_Object
{

    /**
     * @return boolean True if the associated device is a Rack, false otherwise.
     */
    public function isRack()
    {
        return !!$this->getValue('is_rack');
    }

    /**
     * @return boolean True if the associated device can be assigned to Rack, false otherwise.
     */
    public function isRacked()
    {
        return !$this->isRack();
    }

    /**
     * @return boolean True if the associated device is a virtualized, false otherwise.
     */
    public function isVirtual()
    {
        return !!$this->getValue('is_virtual');
    }

    /**
     * @return boolean True if the associated device is powered, false otherwise.
     */
    public function isPowered()
    {
        return $this->isPowerEndpoint() || $this->isPowerDistribution();
    }

    /**
     * @return boolean True if the associated device is a power endpoint, false otherwise.
     */
    public function isPowerEndpoint()
    {
        return $this->getValue('power') == 'endpoint';
    }

    /**
     * @return boolean True if the associated device is a power distribution device, false otherwise.
     */
    public function isPowerDistribution()
    {
        return $this->getValue('power') == 'distribution';
    }

    /**
     * @return boolean True if the associated device is a networked, false otherwise.
     */
    public function isNetworked()
    {
        return $this->isNetworkEndpoint() || $this->isNetworkDistribution();
    }

    /**
     * @return boolean True if the associated device is a network endpoint, false otherwise.
     */
    public function isNetworkEndpoint()
    {
        return $this->getValue('network') == 'endpoint';
    }

    /**
     * @return boolean True if the associated device is a network distribution device, false otherwise.
     */
    public function isNetworkDistribution()
    {
        return $this->getValue('network') == 'distribution';
    }

}
