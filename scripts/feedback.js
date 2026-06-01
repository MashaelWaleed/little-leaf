let currentOffset = 3; // We already showed 3 in PHP
const loadMoreBtn = document.getElementById("loadMoreBtn");
const feedbackList = document.getElementById("feedback-list");

// --- Your Existing Read More AJAX Logic ---
if (loadMoreBtn) {
  loadMoreBtn.addEventListener("click", function () {
    loadMoreBtn.textContent = "Loading...";

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
          feedbackList.insertAdjacentHTML("beforeend", data);
          currentOffset += 5;
          loadMoreBtn.textContent = "Read More Opinions";
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        loadMoreBtn.textContent = "Try Again";
      });
  });
}

// --- Combined Form Handling & New AJAX Form Submission ---
document.addEventListener("DOMContentLoaded", () => {
  const feedbackForm = document.getElementById("feedbackForm");
  const commentsArea = document.getElementById("comments");
  const charCountDisplay = document.getElementById("charCount");

  // 1. Real-time Character Counter
  if (commentsArea && charCountDisplay) {
    commentsArea.addEventListener("input", () => {
      const length = commentsArea.value.length;
      charCountDisplay.textContent = `${length} / 500`;

      if (length >= 450) {
        charCountDisplay.classList.add("char-limit-reached");
      } else {
        charCountDisplay.classList.remove("char-limit-reached");
      }
    });
  }

  // 2. Form Submission AJAX Interception
  if (feedbackForm) {
    feedbackForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const submitBtn = feedbackForm.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      const originalBtnText = submitBtn.textContent;
      submitBtn.textContent = "Submitting...";

      const formData = new FormData(feedbackForm);
      formData.append("feedback-submit", "1");

      fetch(feedbackForm.getAttribute("action"), {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) throw new Error("Network issue encountered.");
          return response.json();
        })
        .then((data) => {
          if (data.status === "success") {
            showGlobalToast(data.message, "success");

            //  Look for an existing card belonging to this user
            const existingCard = document.querySelector(
              `.comment-card[data-user-id="${data.user_id}"]`,
            );

            if (existingCard) {
              //  UX Fix: Update the existing card in place! No duplicates!
              existingCard.querySelector(".comment-sender").innerHTML =
                `<span> From </span>${data.user_name}`;
              existingCard.querySelector(".comment-content").textContent =
                data.comments;
              existingCard.querySelector(".comment-date").textContent =
                data.date;

              // Move it to the top of the feed smoothly since it was just updated
              if (feedbackList && feedbackList.firstChild !== existingCard) {
                feedbackList.insertBefore(
                  existingCard,
                  feedbackList.firstChild,
                );
              }
            } else {
              // Create a completely new card if they haven't posted feedback before
              const newCardHtml = `
              <div class="comment-card" data-user-id="${data.user_id}">
                <h1 class="comment-sender"><span> From </span>${data.user_name}</h1>
                <p class="comment-content">${data.comments}</p>
                <span class="comment-date">${data.date}</span>
              </div>
            `;

              if (feedbackList) {
                feedbackList.insertAdjacentHTML("afterbegin", newCardHtml);
                currentOffset++;
              } else {
                location.reload();
                return;
              }
            }

            feedbackForm.reset();
            if (charCountDisplay) charCountDisplay.textContent = "0 / 500";
          } else {
            showGlobalToast(data.message, "error");
          }
        })
        .catch((error) => {
          showGlobalToast(
            "Connection error. Feedback could not be registered.",
            "error",
          );
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.textContent = originalBtnText;
        });
    });
  }
});
