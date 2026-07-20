<?php

return [
    'min_common_items' => (int) env('RECOMMENDATION_MIN_COMMON_ITEMS', 3),
    'significance_threshold' => (int) env('RECOMMENDATION_SIGNIFICANCE_THRESHOLD', 5),
    'min_similarity' => (float) env('RECOMMENDATION_MIN_SIMILARITY', 0.20),
    'max_neighbors' => (int) env('RECOMMENDATION_MAX_NEIGHBORS', 30),
    'bayesian_min_votes' => (int) env('RECOMMENDATION_BAYESIAN_MIN_VOTES', 20),
];
