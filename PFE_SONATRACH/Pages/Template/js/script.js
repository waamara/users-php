document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const profileImage = document.querySelector('.profile img');
    const profileDropdown = document.querySelector('.profile-link');
    const menuIcons = document.querySelectorAll('.content-data .menu .icon');
    const allDropdowns = document.querySelectorAll('.side-dropdown');

    // Sidebar Dropdowns
    allDropdowns.forEach(dropdown => {
        const trigger = dropdown.previousElementSibling;

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            dropdown.classList.toggle('show');
            trigger.classList.toggle('active');
        });
    });

    // Profile Dropdown
    profileImage.addEventListener('click', (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle('show');
    });

    // Menu Dropdowns
    menuIcons.forEach(icon => {
        icon.addEventListener('click', (e) => {
            e.stopPropagation();
            const menu = icon.parentElement.nextElementSibling;
            menu.classList.toggle('show');
        });
    });

    // Close dropdowns on outside click
    window.addEventListener('click', (e) => {
        if (!profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove('show');
        }

        document.querySelectorAll('.menu-link').forEach(menu => {
            if (!menu.parentElement.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
    });

    // Progress bars
    document.querySelectorAll('.progress').forEach(bar => {
        bar.style.setProperty('--value', bar.dataset.value);
    });

    // Initialize components
    function initializeComponents() {
        if (document.getElementById('chart')) {
            const options = {
                series: [{
                    name: 'series1',
                    data: [31, 40, 28, 51, 42, 109, 100]
                }, {
                    name: 'series2',
                    data: [11, 32, 45, 32, 34, 52, 41]
                }],
                chart: {
                    height: 350,
                    type: 'area'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth'
                },
                xaxis: {
                    type: 'datetime',
                    categories: [
                        "2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z",
                        "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z",
                        "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z",
                        "2018-09-19T06:30:00.000Z"
                    ]
                },
                tooltip: {
                    x: {
                        format: 'dd/MM/yy HH:mm'
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#chart"), options);
            chart.render();
        }
    }

    // Initial component setup
    initializeComponents();
});
