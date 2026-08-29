<!DOCTYPE html>
<html class="light" lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Milky Way d\' Antsirabe' ?></title>

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="shortcut icon" href="/favicon.ico">
<link rel="apple-touch-icon" href="/favicon.ico">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind Config -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-tertiary": "#ffffff",
                        "on-primary": "#ffffff",
                        "on-secondary": "#ffffff",
                        "on-error": "#ffffff",
                        "inverse-surface": "#303030",
                        "inverse-on-surface": "#f2f0f0",
                        "primary-fixed": "#cbe6ff",
                        "on-error-container": "#93000a",
                        "error-container": "#ffdad6",
                        "surface-variant": "#e4e2e2",
                        "secondary-fixed-dim": "#8fd5b6",
                        "on-primary-fixed-variant": "#154b6d",
                        "on-tertiary-fixed-variant": "#474743",
                        "on-secondary-fixed": "#002115",
                        "secondary-container": "#a8eece",
                        "tertiary-fixed-dim": "#c9c6c1",
                        "error": "#ba1a1a",
                        "tertiary": "#40403c",
                        "on-primary-container": "#a4d2fb",
                        "on-secondary-fixed-variant": "#00513a",
                        "on-surface-variant": "#41474e",
                        "tertiary-fixed": "#e5e2dc",
                        "secondary-fixed": "#abf1d1",
                        "secondary": "#246a51",
                        "surface": "#fbf9f8",
                        "surface-container": "#efeded",
                        "surface-container-high": "#eae8e7",
                        "on-tertiary-fixed": "#1c1c18",
                        "on-surface": "#1b1c1c",
                        "primary-container": "#2a5b7e",
                        "surface-container-low": "#f5f3f3",
                        "surface-tint": "#336386",
                        "on-tertiary-container": "#cfcdc7",
                        "primary": "#084365",
                        "surface-container-lowest": "#ffffff",
                        "background": "#fbf9f8",
                        "outline-variant": "#c1c7ce",
                        "on-secondary-container": "#296e55",
                        "inverse-primary": "#9dccf4",
                        "primary-fixed-dim": "#9dccf4",
                        "on-primary-fixed": "#001e31",
                        "surface-dim": "#dbd9d9",
                        "surface-container-highest": "#e4e2e2",
                        "surface-bright": "#fbf9f8",
                        "on-background": "#1b1c1c",
                        "tertiary-container": "#585753",
                        "outline": "#72787e"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    spacing: {
                        "margin-desktop": "48px",
                        base: "8px",
                        lg: "40px",
                        sm: "12px",
                        "margin-mobile": "16px",
                        gutter: "24px",
                        xl: "64px",
                        xs: "4px",
                        md: "24px"
                    },
                    fontFamily: {
                        "display-lg": ["Work Sans"],
                        "headline-md": ["Work Sans"],
                        "headline-sm": ["Work Sans"],
                        "body-lg": ["Work Sans"],
                        "label-sm": ["Work Sans"],
                        "label-md": ["Work Sans"],
                        "body-md": ["Work Sans"]
                    },
                    fontSize: {
                        "display-lg": ["40px", { lineHeight: "48px", letterSpacing: "-0.02em", fontWeight: "600" }],
                        "headline-md": ["28px", { lineHeight: "36px", fontWeight: "600" }],
                        "headline-sm": ["22px", { lineHeight: "28px", fontWeight: "500" }],
                        "body-lg": ["18px", { lineHeight: "28px", fontWeight: "400" }],
                        "label-sm": ["12px", { lineHeight: "16px", fontWeight: "500" }],
                        "label-md": ["14px", { lineHeight: "20px", letterSpacing: "0.01em", fontWeight: "600" }],
                        "body-md": ["16px", { lineHeight: "24px", fontWeight: "400" }]
                    }
                }
            }
        }
    </script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="flex flex-row min-h-screen text-on-surface font-body-md">
    <!-- Sidebar en premier -->
    <?= $this->include('partials/sidebar') ?>

    <!-- Ici s'affiche le contenu des pages filles -->
    <?= $this->renderSection('content') ?>

    <!-- Footer commun (scripts) -->
    <?= $this->include('partials/footer') ?>
</body>
</html>