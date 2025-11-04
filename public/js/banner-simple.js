(async function() {
  const zone = "header";
  const site = "blog.com";
  const container = document.getElementById("zone-header");
  const baseURL = "http://banner-system.test:8080";

  try {
    const res = await fetch(`${baseURL}/api/banners?zone=${zone}&site=${site}`);
    const data = await res.json();
    container.innerHTML = data.html;
    container.style.border = "1px solid #ccc";
    container.style.display = "block";
    console.log("Banner HTML insertado:", data.html);
  } catch (e) {
    console.error("Error:", e);
  }
})();
