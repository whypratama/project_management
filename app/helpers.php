<?php

if (!function_exists('getTimelineDetails')) {
    /**
     * Mendapatkan detail ikon dan kelas badge untuk timeline.
     *
     * @param string $type
     * @return array
     */
    function getTimelineDetails($type)
    {
        switch (strtolower($type)) {
            case 'proyek':
                return ['icon' => 'ti ti-flag-checkered', 'badge_class' => 'border-primary'];
            case 'tugas':
                return ['icon' => 'ti ti-list-check', 'badge_class' => 'border-info'];
            case 'file':
                return ['icon' => 'ti ti-file-text', 'badge_class' => 'border-purple'];
            case 'diskusi':
                return ['icon' => 'ti ti-message-dots', 'badge_class' => 'border-warning'];
            default:
                return ['icon' => 'ti ti-clock', 'badge_class' => 'border-secondary'];
        }
    }
}
