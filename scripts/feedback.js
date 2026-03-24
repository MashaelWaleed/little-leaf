let currentOffset = 3; // We already showed 3 in PHP
const loadMoreBtn = document.getElementById("loadMoreBtn");
const feedbackList = document.getElementById("feedback-list");

loadMoreBtn.addEventListener("click", function () {
  // 1. Change button text to show it's working
  loadMoreBtn.textContent = "Loading...";

  // 2. Fetch from our PHP file
  fetch(`../server/fetch_feedback.php?offset=${currentOffset}`)
    .then((response) => response.text())
    .then((data) => {
      if (data.trim() === "done") {
        loadMoreBtn.textContent = "No more opinions to show";
        loadMoreBtn.disabled = true;
        loadMoreBtn.style.opacity = "0.5";
        loadMoreBtn.style.background = "none";
        loadMoreBtn.style.color = "black";
      } else {
        // 3. Append the new cards to the list
        feedbackList.insertAdjacentHTML("beforeend", data);

        // 4. Update offset and reset button
        currentOffset += 5;
        loadMoreBtn.textContent = "Read More Opinions";
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      loadMoreBtn.textContent = "Try Again";
    });
});
