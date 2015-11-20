<?php

/**
 * Represents an instance of a Sales Device resource from the API.
 * Holds data for a Sales Device and provides methods to retreive
 * objects related to the Sales Device such as its Assets and Type.
 *
 * @category   ColoCrossing
 * @package    ColoCrossing_Object
 */
class ColoCrossing_Object_SalesDevice extends ColoCrossing_Resource_Object
{

    /**
     * Retrieves the Device Type object that describes the capabilities
     * of this device. Such as its power, network, and rack capabilities.
     * @return ColoCrossing_Object_Type  The Type
     */
    public function getType()
    {
        return $this->getObject('type', null, 'type');
    }

    /**
     * Retrieves the list of Device Asset objects.
     * @param  array    $options        The Options of the page and sorting.
     * @return ColoCrossing_Collection<ColoCrossing_Object_Asset>    The Device Assets
     */
    public function getAssets(array $options = null)
    {
        return $this->getResourceChildCollection('assets', $options);
    }

    /**
     * Retrieves the Device Asset object matching the provided Id.
     * @param  int      $id                             The Id.
     * @return ColoCrossing_Object_Asset|null    The Device Asset
     */
    public function getAsset($id)
    {
        return $this->getResourceChildObject('assets', $id);
    }

}
