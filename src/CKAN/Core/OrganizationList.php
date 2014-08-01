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

        if (null === $this->json = json_decode($json, true)) { //decode as array
            die('Fatal: could not decode json');
        }
    }

    /**
     * @param $organization
     *
     * @return array
     */
    public function getTreeArrayFor($organization)
    {
        $return = [];
        $parent = [];

        $organization = trim($organization);
        foreach ($this->json['taxonomies'] as $taxonomy) {

            if ($taxonomy['taxonomy']['unique id'] !== $taxonomy['taxonomy']['term']) {
//                skip 3rd level children
                continue;
            }

            if ($organization == trim($taxonomy['taxonomy']['Federal Agency'])) {
                if (!$taxonomy['taxonomy']['Sub Agency']) {
//                    let's put parent agency first
                    define('PARENT_TERM', $taxonomy['taxonomy']['unique id']);
                    $parent[$taxonomy['taxonomy']['unique id']] = $organization;
                } else {
                    $return[$taxonomy['taxonomy']['unique id']] = $taxonomy['taxonomy']['Sub Agency'];
                }
            }
        }

        return array_merge($parent, $return);
    }

    /**
     * @param $organization
     *
     * @return bool
     */
    public function getTermFor($organization)
    {
        $term = false;
        $organization = trim($organization);
        foreach ($this->json['taxonomies'] as $taxonomy) {
            if ($taxonomy['taxonomy']['unique id'] !== $taxonomy['taxonomy']['term']) {
//                skip 3rd level children
                continue;
            }
            if ($organization == trim($taxonomy['taxonomy']['Federal Agency'])) {
                if ('' == trim($taxonomy['taxonomy']['Sub Agency'])) {
                    $term = $taxonomy['taxonomy']['unique id'];
                    break;
                }
            }
            if ($organization == trim($taxonomy['taxonomy']['Sub Agency'])) {
                $term = $taxonomy['taxonomy']['unique id'];
                break;
            }
        }

        return $term;
    }

    /**
     * @param string $rootAgency
     *
     * @return array
     */
    public function getTreeArray($rootAgency = null)
    {
        $return     = [];
        $taxonomies = [];

        /**
         * Sort & extract
         */
        foreach ($this->json['taxonomies'] as $taxonomy) {
            unset($taxonomy['taxonomy']['vocabulary']);
            unset($taxonomy['taxonomy']['term']);

            $taxonomy['taxonomy']['Federal Agency'] = trim($taxonomy['taxonomy']['Federal Agency']);
            $taxonomy['taxonomy']['Sub Agency']     = trim($taxonomy['taxonomy']['Sub Agency']);
            $taxonomy['taxonomy']['id']             = trim($taxonomy['taxonomy']['unique id']);

            if (!$taxonomy['taxonomy']['Federal Agency'] || !$taxonomy['taxonomy']['id']) {
                continue;
            }

            if ($taxonomy['taxonomy']['Sub Agency']) {
//                sub-agency
                $taxonomy['taxonomy']['root'] = false;

//                putting in the tail of array
                $taxonomies[] = $taxonomy['taxonomy'];
            } else {
//                root agency
                $taxonomy['taxonomy']['root'] = true;
//                putting in the head of array
                array_unshift($taxonomies, $taxonomy['taxonomy']);
            }
        }

        /**
         * Temporary fix for missing root agency
         * Executive Office of the President [eop-gov]
         */
        $return['Executive Office of the President'] =
            [
                'id'       => 'eop-gov',
                'is_cfo'   => 'N',
                'children' => []
            ];

        /**
         * Full scan
         */
        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy['root']) {
                if (isset($return[$taxonomy['Federal Agency']])) {
                    continue;
                }
                $return[$taxonomy['Federal Agency']] =
                    [
                        'id'       => $taxonomy['id'],
                        'is_cfo'   => $taxonomy['is_cfo'],
                        'children' => []
                    ];
            } else {
                if (isset($return[$taxonomy['Federal Agency']])) {
                    if (isset($return[$taxonomy['Federal Agency']]['children'][$taxonomy['Sub Agency']])) {
                        continue;
                    }
                    $return[$taxonomy['Federal Agency']]['children'][$taxonomy['Sub Agency']] =
                        [
                            'id'     => $taxonomy['id'],
                            'is_cfo' => $taxonomy['is_cfo'],
                        ];

                } else {
//                    echo "Parent not found: " . $taxonomy['Federal Agency'] .
                    echo ' [' . $taxonomy['Sub Agency'] . '] ' . $taxonomy['id'] . PHP_EOL;
                }
            }
        }

        sort($return);

        if ($rootAgency) {
            if (isset($return[$rootAgency])) {
                $return = $return[$rootAgency];
            } else {
                $return = [];
            }
        }

        return $return;
    }
}