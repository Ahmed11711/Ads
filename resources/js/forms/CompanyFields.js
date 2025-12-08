export const fields = [
  { key: "name", label: "Name", required: 1, placeholder: "Enter Name", type: "text", isString: false },
  { key: "logo", label: "Logo", required: 1, placeholder: "Enter Logo", type: "image", isString: true },
  { key: "type", label: "Type", required: 1, placeholder: "Enter Type", type: "select", isString: false,
      options: [
    {
        "value": "ads",
        "label": "Ads"
    },
    {
        "value": "tasks",
        "label": "Tasks"
    },
    {
        "value": "survey",
        "label": "Survey"
    },
    {
        "value": "games",
        "label": "Games"
    },
    {
        "value": "other",
        "label": "Other"
    }
] },
  { key: "status", label: "Status", required: 1, placeholder: "Enter Status", type: "select", isString: false,
      options: [
    {
        "value": "active",
        "label": "Active"
    },
    {
        "value": "inactive",
        "label": "Inactive"
    }
] },
  { key: "description", label: "Description", required: 1, placeholder: "Enter Description", type: "textarea", isString: false },
  { key: "amount", label: "Amount", required: 1, placeholder: "Enter Amount", type: "text", isString: false },
  { key: "url", label: "Url", required: 1, placeholder: "Enter Url", type: "textarea", isString: false }
];