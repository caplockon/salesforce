<?php


namespace Twobes\Salesforce\Models;


class SObjectModel extends BaseModel
{
    protected $objectName = "";

    /**
     * Retrieve object by id
     * @param string $id
     * @return false|array
     */
    public function get($id)
    {
        return $this->client->util->getObject($this->getObjectName(), $id);
    }

    /**
     * Create object in Salesforce
     * @param array $data
     * @return false|array
     */
    public function create($data)
    {
        return $this->client->util->createObject($this->getObjectName(), $data);
    }

    /**
     * Update object in Salesforce
     * Return: TRUE if successful, FALSE if failed
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        return $this->client->util->updateObject($this->getObjectName(), $id, $data);
    }

    /**
     * Delete an object from Salesforce
     * Return: TRUE if successful, FALSE if failed
     * @param string $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->client->util->deleteObject($this->getObjectName(), $id);
    }

    /**
     * Get list of object fields
     * @return false|array
     */
    public function describe()
    {
        return $this->client->util->describe($this->getObjectName());
    }

    /**
     * Get basic information of object
     * @return false|array
     */
    public function basicInformation()
    {
        return $this->client->util->getBasicObjectInformation($this->getObjectName());
    }

    /**
     * Search object in Salesforce
     * @param string $select
     * @param array $conditions
     * @return false|array
     */
    public function search($select = "*", $conditions = [])
    {
        $query = "SELECT {$select} FROM {$this->getObjectName()}";
        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        return $this->client->util->executeSOQL($query);
    }

    /**
     * Set Salesforce object name
     * @param string $name
     */
    public function setObjectName($name)
    {
        $this->objectName = $name;
    }

    /**
     * Get Salesforce object name
     * @return string
     */
    public function getObjectName()
    {
        $objectName = $this->objectName;

        if ($objectName === false || strlen($objectName) === 0) {
            $className = static::class;
            $pos = strrpos($className, "\\"); // Last character "\"
            $this->objectName = $pos === false ? $className : substr($className, $pos + 1);
        }

        return $this->objectName;
    }
}