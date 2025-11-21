// Navbar 
const navbarHTML = `
  <div class="navbar">
    <a href="index.php"><img src="images/favicon.png" width=50px height=50px href="index.html"></a>
    <a href="index.php">Hovedside</a>
    <a href="bookshelf.php">Bokhylle</a>    
  </div>
`;

// Inject navbar at the top of the body
function injectNavbar() {
  document.body.insertAdjacentHTML('afterbegin', navbarHTML);
  // Highlight active link
  document.querySelectorAll('.navbar a').forEach(link => {
    if (window.location.pathname.endsWith(link.getAttribute('href'))) {
      link.classList.add('active');
    }
  });
}

// On DOMContentLoaded, inject navbar and render members if needed
window.addEventListener('DOMContentLoaded', () => {
  injectNavbar();

  const closeBtn = document.querySelector(".close");

});