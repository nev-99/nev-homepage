window.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("cmd");
  const guide = document.getElementById("terminal-guide");

  const routes = {
    "cd blog": "fools.html",
    "cd comic": "comic.html",
    "cd room": "room_101.html",
  };

  input.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      const cmd = this.value.trim();

      if (cmd === "help") {
        guide.innerHTML = `
Available commands:

> cd blog   - open blog page
> cd comic   - open comic section
> cd room    - enter NEV's room

System:
> back       - go back
> forward    - go forward
        `;
      } else if (routes[cmd]) {
        window.location.href = routes[cmd];
      } else if (cmd === "back") {
        history.back();
      } else if (cmd === "forward") {
        history.forward();
      } else {
        guide.innerHTML = `Command not found: ${cmd}`;
      }

      this.value = "";
    }
  });
});
