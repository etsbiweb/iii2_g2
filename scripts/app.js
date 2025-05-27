document.addEventListener('DOMContentLoaded', function() {
    const dropdownContainers = document.querySelectorAll('.dropdown-container');

    dropdownContainers.forEach(container => {
        const dropdownToggle = container.querySelector('a.dropdown-toggle');
        
        if (dropdownToggle) {
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                dropdownContainers.forEach(otherContainer => {
                    if (otherContainer !== container && otherContainer.classList.contains('show')) {
                        otherContainer.classList.remove('show');
                        otherContainer.querySelectorAll('.has-submenu.show').forEach(sub => sub.classList.remove('show'));
                    }
                });

                container.classList.toggle('show');

                if (!container.classList.contains('show')) {
                    container.querySelectorAll('.has-submenu.show').forEach(sub => sub.classList.remove('show'));
                }
            });
        }

        const hasSubmenus = container.querySelectorAll('.has-submenu');
        hasSubmenus.forEach(submenuParent => {
            const submenuToggle = submenuParent.querySelector('a.dropdown-toggle');
            
            if (submenuToggle) {
                submenuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    hasSubmenus.forEach(otherSubmenuParent => {
                        if (otherSubmenuParent !== submenuParent && otherSubmenuParent.classList.contains('show')) {
                            otherSubmenuParent.classList.remove('show');
                        }
                    });

                    submenuParent.classList.toggle('show');
                });
            }
        });
    });

    document.addEventListener('click', function(e) {
        dropdownContainers.forEach(container => {
            if (!container.contains(e.target) && container.classList.contains('show')) {
                container.classList.remove('show');
                container.querySelectorAll('.has-submenu.show').forEach(sub => sub.classList.remove('show'));
            }
        });
    });
});