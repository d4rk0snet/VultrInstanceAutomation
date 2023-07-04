<?php

namespace D4rk0s\Vultr\Services;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Vultr\VultrPhp\Services\Instances\Instance;
use Vultr\VultrPhp\Services\Instances\InstanceCreate;
use Vultr\VultrPhp\Services\Snapshots\Snapshot;
use Vultr\VultrPhp\VultrClient;

class VultrService
{
    private static ?VultrClient $vultrClient = null;
    private static ?Instance $currentInstance = null;

    public static function getVultrClient() : VultrClient
    {
        if(self::$vultrClient !== null) {
            return self::$vultrClient;
        }

        $dotenv = Dotenv::createImmutable(__DIR__."/../../");
        $dotenv->load();
        $dotenv->required('VULTR_API_KEY')->notEmpty();
        $dotenv->required('INSTANCE_REGION')->notEmpty();
        $dotenv->required('INSTANCE_PLAN')->notEmpty();
        $dotenv->required('SNAPSHOT_LABEL')->notEmpty();
        $dotenv->required('INSTANCE_LABEL')->notEmpty();

        $http_factory = new HttpFactory();
        $client = VultrClient::create($_ENV['VULTR_API_KEY'], new Client(), $http_factory, $http_factory);

        self::$vultrClient = $client;

        return $client;
    }

    public static function getCurrentInstance() : ?Instance
    {
        if(self::$currentInstance !== null) {
            return self::$currentInstance;
        }

        $instanceArray = self::getVultrClient()->instances->getInstances(['label' => $_ENV['INSTANCE_LABEL']]);
        if(count($instanceArray)) {
            self::$currentInstance = current($instanceArray);
            return self::$currentInstance;
        }

        return null;
    }

    public static function getCurrentSnapshot() : ?Snapshot
    {
        $snapshotArray = self::getVultrClient()->snapshots->getSnapshots($_ENV['SNAPSHOT_LABEL']);

        return count($snapshotArray) ? current($snapshotArray) : null;
    }

    public static function createSnapshot() : ?Snapshot
    {
        $currentInstance = self::getCurrentInstance();

        return $currentInstance !== null ?
          self::getVultrClient()->snapshots->createSnapshot(self::getCurrentInstance()->getId(), $_ENV['SNAPSHOT_LABEL']) :
          null;
    }

    public static function createInstance() : Instance
    {
        $instanceCreate = new InstanceCreate($_ENV['INSTANCE_REGION'], $_ENV['INSTANCE_PLAN']);
        $instanceCreate->setSnapshotId(self::getCurrentSnapshot()->getId());
        $instanceCreate->setLabel($_ENV['INSTANCE_LABEL']);

        return self::getVultrClient()->instances->createInstance($instanceCreate);
    }
}