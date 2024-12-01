   // kani na code kay para sa footericon na kana bitaw i click nimo mo change dayon siya color like mo active pero ang uban dili active ang color
        const currentPage = window.location.pathname.split("/").pop();
        document.querySelectorAll('.footericon').forEach(icon => {
            if (icon.getAttribute('data-page') === currentPage) {
                icon.classList.add('active');
            }
        });


        
