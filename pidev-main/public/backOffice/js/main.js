document.addEventListener('DOMContentLoaded', function() {
  // Toggle profile menu
  document.getElementById('profile-icon').addEventListener('click', function() {
      document.getElementById('profile-menu').classList.toggle('active');
  });
  
  // Close profile menu when clicking outside
  document.addEventListener('click', function(event) {
      const profileMenu = document.getElementById('profile-menu');
      if (!profileMenu.contains(event.target) && !document.getElementById('profile-icon').contains(event.target)) {
          profileMenu.classList.remove('active');
      }
  });
});

// add hovered class to selected list item
let list = document.querySelectorAll(".navigation li");

function activeLink() {
  list.forEach((item) => {
    item.classList.remove("hovered");
  });
  this.classList.add("hovered");
}

list.forEach((item) => item.addEventListener("mouseover", activeLink));

// Menu Toggle
let toggle = document.querySelector(".toggle");
let navigation = document.querySelector(".navigation");
let main = document.querySelector(".main");

toggle.onclick = function () {
  navigation.classList.toggle("active");
  main.classList.toggle("active");
};



