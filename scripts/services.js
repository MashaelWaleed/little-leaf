document.addEventListener("DOMContentLoaded", () => {
  const openSearchFromService = document.getElementById(
    "openSearchFromService",
  );

  // open search reuse
  if (openSearchFromService && typeof openSearch !== "undefined") {
    openSearchFromService.addEventListener("click", () => {
      openSearch.click();
    });
  }
});
