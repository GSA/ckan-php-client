<?php

namespace CKAN\Core;

use Exception;

/**
 * @author Alex Perfilov
 * @date   2/20/14
 */
class OrganizationList
{
    /**
     * @var string
     */
    private $jsonUrl = 'http://idm.data.gov/fed_agency.json';

    /**
     * @var mixed|null
     */
    private $json = null;

    /**
     * @param null $jsonUrl
     */
    public function __construct($jsonUrl = null)
    {
        if ($jsonUrl) {
            $this->jsonUrl = $jsonUrl;
        }

        try {
            $json = file_get_contents($this->jsonUrl);
        } catch (Exception $ex) {
            echo('Fatal: could not get organization list from json ' . $this->jsonUrl . PHP_EOL);
            die($ex->getMessage());
        }

//        decode as array
        if (null === $this->json = json_decode($json, true)) {
            die('Fatal: could not decode json');
        }
    }

    /**
     * @param $organization
     * @return array
     */
    public function getTreeArrayFor($organization)
    {
        $return = array();

        $organization = trim($organization);
        foreach ($this->json['taxonomies'] as $taxonomy) {
            if ($organization == trim($taxonomy['taxonomy']['Federal Agency'])) {
                $return[] = $taxonomy['taxonomy']['term'];
            }
        }

        return array_unique($return);
    }
}