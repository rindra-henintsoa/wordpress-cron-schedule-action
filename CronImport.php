<?php

namespace Specifs\SessionFormation;

/**
 * gestion du cron de l'import des sessions de formation
 */
class CronImport
{
    /**
     * init du cron
     *
     * @return void
     */
    public function init()
    {
        //enregistre une nouvelle fréquence d'éxécution
        add_filter( 'cron_schedules', function()
        {
            $schedules['every_5_minute'] = array(
                'interval' => 300, // Every 5 min
                'display'  => __( 'Every 5 minutes' ),
            );
            return $schedules;
        });

        //Schedule an action
        // if ( ! wp_next_scheduled( array($this,"runDaily") ) ) {
        //     wp_schedule_event( time(), 'daily', array($this,"runDaily"));
        // }

        // if ( ! wp_next_scheduled( array($this,"runEvery5min") ) ) {
        //     wp_schedule_event( time(), 'every_5_minute', array($this,"runEvery5min"));
        // }

        // @changelog [FIX] (Henintsoa) Optimisatinon du programmation du cron
        // J'ai remplacé le paramètre " array($this,"runDaily") "
        // par le nom de hook personnalisé car ça retounais des fatals.
        if ( ! wp_next_scheduled( 'import_trigger_cron_hook' )) {
            wp_schedule_event( strtotime('00:00:00'), 'daily', 'import_trigger_cron_hook');
        }

        if ( ! wp_next_scheduled( 'import_processing_cron_hook' ) ) {
            wp_schedule_event( strtotime('00:00:00'), 'daily', 'import_processing_cron_hook');
        }

        add_action( 'import_trigger_cron_hook',  array($this, "runDaily") );
        add_action( 'import_processing_cron_hook',  array($this, "runEvery5min") );
    }

    public function runDaily()
    {
        file_get_contents(site_url().'/wp-load.php?import_key=1ArAJ7O&import_id=1&action=trigger');
    }

    public function runEvery5min()
    {
        file_get_contents(site_url().'/wp-load.php?import_key=1ArAJ7O&import_id=1&action=processing');
    }
}