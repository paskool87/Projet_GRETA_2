import "./Input.scss";

function Input({ label, ...props }) {
  return (
    <div className="input">
      <label className="input-label">{label}</label>
      <input {...props} className="input-input"/>
    </div>
  );
}

export default Input;