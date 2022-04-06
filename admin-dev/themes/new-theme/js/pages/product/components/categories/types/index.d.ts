type Category = {
  id: number,
  name: string,
  preview: string,
  breadcrumb: string
}

type TypeaheadCategory = {
  id: number,
  name: string,
  breadcrumb: string,
}

type TreeCategory = {
  id: number,
  name: string,
  active: boolean,
  breadcrumb: string,
  children: Array<TreeCategory>,
}
